<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PortfolioWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_a_portfolio_from_the_six_step_form(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('portfolios.store'), [
            'full_name' => 'Avery Student',
            'email' => 'avery@example.com',
            'about_me' => 'I build useful things.',
            'education_text' => "Computer Science, Example University\n",
            'skills_text' => "PHP\nLaravel",
            'projects' => [
                [
                    'title' => 'Portfolio Generator',
                    'description' => 'A project showcase.',
                    'tech_stack' => 'Laravel, PostgreSQL',
                    'github_url' => 'https://github.com/example/project',
                    'live_demo_url' => 'https://example.test',
                ],
            ],
            'experience_text' => 'Software Intern, Example Co.',
            'social_links_text' => "https://github.com/avery\nhttps://example.test",
            'template' => 'simple',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('portfolios', [
            'user_id' => $user->id,
            'full_name' => 'Avery Student',
            'template' => 'simple',
            'is_published' => false,
        ]);

        $portfolio = Portfolio::query()->where('user_id', $user->id)->firstOrFail();

        $this->assertSame(['Computer Science, Example University'], $portfolio->education);
        $this->assertSame(['PHP', 'Laravel'], $portfolio->skills);
        $this->assertSame(['Software Intern, Example Co.'], $portfolio->experience);
        $this->assertSame(['https://github.com/avery', 'https://example.test'], $portfolio->social_links);
        $this->assertSame('Portfolio Generator', $portfolio->projects[0]['title']);
    }

    public function test_only_http_links_are_accepted_for_projects_and_social_links(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from(route('portfolios.create'))->post(route('portfolios.store'), [
            'full_name' => 'Avery Student',
            'email' => 'avery@example.com',
            'projects' => [
                ['title' => 'Unsafe link', 'github_url' => 'javascript:alert(1)'],
            ],
            'social_links_text' => 'javascript:alert(1)',
        ]);

        $response
            ->assertRedirect(route('portfolios.create'))
            ->assertSessionHasErrors(['projects.0.github_url', 'social_links.0']);

        $this->assertDatabaseCount('portfolios', 0);
    }

    public function test_portfolio_full_name_cannot_contain_numbers(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from(route('portfolios.create'))->post(route('portfolios.store'), [
            'full_name' => '12345',
            'email' => 'avery@example.com',
        ]);

        $response
            ->assertRedirect(route('portfolios.create'))
            ->assertSessionHasErrors([
                'full_name' => 'The full name format is invalid.',
            ]);

        $this->assertDatabaseCount('portfolios', 0);
    }

    public function test_create_form_exposes_six_steps_and_the_live_quality_tools(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('portfolios.create'))
            ->assertOk()
            ->assertSee('Personal')
            ->assertSee('Education')
            ->assertSee('Skills')
            ->assertSee('Projects')
            ->assertSee('Experience')
            ->assertSee('Links')
            ->assertSee('Live draft preview')
            ->assertSee('Portfolio quality');
    }

    public function test_selected_template_page_has_exactly_three_designs(): void
    {
        $user = User::factory()->create();
        $portfolio = $this->createPortfolio($user);

        $response = $this->actingAs($user)
            ->get(route('portfolios.templates.select', $portfolio))
            ->assertOk()
            ->assertSee('Simple')
            ->assertSee('Modern')
            ->assertSee('Creative');

        $this->assertSame(3, substr_count($response->getContent(), 'name="template"'));
    }

    public function test_selected_template_is_saved_and_used_by_the_preview(): void
    {
        $user = User::factory()->create();
        $portfolio = $this->createPortfolio($user);

        $this->actingAs($user)
            ->patch(route('portfolios.template.choose', $portfolio), ['template' => 'creative'])
            ->assertRedirect(route('portfolios.preview', $portfolio));

        $this->assertDatabaseHas('portfolios', [
            'id' => $portfolio->id,
            'template' => 'creative',
        ]);

        $this->get(route('portfolios.preview', $portfolio))
            ->assertOk()
            ->assertSee('Creative Portfolio / 01')
            ->assertDontSee('grayscale');
    }

    public function test_profile_picture_and_project_screenshot_use_the_configured_storage_disk(): void
    {
        Storage::fake('public');
        config(['filesystems.portfolio_disk' => 'public']);
        $user = User::factory()->create();
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC');

        $this->actingAs($user)->post(route('portfolios.store'), [
            'full_name' => 'Avery Student',
            'email' => 'avery@example.com',
            'profile_picture' => UploadedFile::fake()->createWithContent('profile.png', $png),
            'projects' => [
                [
                    'title' => 'Portfolio Generator',
                    'screenshot' => UploadedFile::fake()->createWithContent('project.png', $png),
                ],
            ],
            'projects_present' => '1',
        ])->assertRedirect();

        $portfolio = Portfolio::query()->where('user_id', $user->id)->firstOrFail();
        Storage::disk('public')->assertExists($portfolio->profile_picture);
        Storage::disk('public')->assertExists($portfolio->projects[0]['screenshot']);
    }

    public function test_local_profile_picture_uses_the_current_request_host(): void
    {
        config([
            'app.url' => 'http://localhost:8000',
            'filesystems.portfolio_disk' => 'public',
            'filesystems.disks.public.url' => 'http://localhost:8000/storage',
        ]);
        $user = User::factory()->create();
        $portfolio = $this->createPortfolio($user, [
            'profile_picture' => 'profiles/profile.png',
        ]);

        $this->actingAs($user)
            ->get('http://127.0.0.1:8017'.route('portfolios.preview', $portfolio, false))
            ->assertOk()
            ->assertSee('http://127.0.0.1:8017/storage/profiles/profile.png');
    }

    public function test_removing_saved_images_updates_the_database_and_cleans_cloud_storage(): void
    {
        Storage::fake('public');
        config(['filesystems.portfolio_disk' => 'public']);
        $user = User::factory()->create();
        $profilePath = 'profiles/old-profile.png';
        $screenshotPath = 'project-screenshots/old-project.png';
        Storage::disk('public')->put($profilePath, 'profile');
        Storage::disk('public')->put($screenshotPath, 'screenshot');
        $portfolio = $this->createPortfolio($user, [
            'profile_picture' => $profilePath,
            'projects' => [
                ['title' => 'Project', 'screenshot' => $screenshotPath],
            ],
        ]);

        $this->actingAs($user)->put(route('portfolios.update', $portfolio), [
            'full_name' => $portfolio->full_name,
            'email' => $portfolio->email,
            'remove_profile_picture' => '1',
            'projects_present' => '1',
            'projects' => [
                ['title' => 'Project', 'remove_screenshot' => '1'],
            ],
        ])->assertRedirect(route('portfolios.preview', $portfolio));

        $portfolio->refresh();
        $this->assertNull($portfolio->profile_picture);
        $this->assertNull($portfolio->projects[0]['screenshot']);
        Storage::disk('public')->assertMissing([$profilePath, $screenshotPath]);
    }

    public function test_manage_page_has_view_edit_delete_and_qr_actions(): void
    {
        $user = User::factory()->create();
        $portfolio = $this->createPortfolio($user, [
            'is_published' => true,
        ]);

        $this->actingAs($user)
            ->get(route('portfolios.manage'))
            ->assertOk()
            ->assertSee(route('portfolios.preview', $portfolio))
            ->assertSee(route('portfolios.edit', $portfolio))
            ->assertSee('View')
            ->assertSee('Edit')
            ->assertSee('Delete')
            ->assertSee('Download QR code')
            ->assertSee('Delete this portfolio permanently?');
    }

    public function test_homepage_lists_only_recently_published_portfolios(): void
    {
        $user = User::factory()->create();
        $published = $this->createPortfolio($user, [
            'full_name' => 'Published Student',
            'slug' => 'published-student',
            'is_published' => true,
        ]);
        $this->createPortfolio($user, [
            'full_name' => 'Draft Student',
            'slug' => 'draft-student',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee($published->full_name)
            ->assertDontSee('Draft Student');
    }

    public function test_published_public_page_uses_the_selected_template_and_has_a_downloadable_qr_code(): void
    {
        $user = User::factory()->create();
        $portfolio = $this->createPortfolio($user, ['template' => 'modern']);

        $this->actingAs($user)
            ->post(route('portfolios.publish', $portfolio))
            ->assertRedirect(route('portfolios.manage'));

        $this->get(route('portfolios.public', $portfolio->slug))
            ->assertOk()
            ->assertSee('01 / Introduction')
            ->assertSee('data-portfolio-qr', false)
            ->assertSee('Download QR code');
    }

    public function test_another_user_cannot_view_or_change_a_portfolio(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $portfolio = $this->createPortfolio($owner);

        $this->actingAs($otherUser)
            ->get(route('portfolios.preview', $portfolio))
            ->assertNotFound();

        $this->actingAs($otherUser)
            ->delete(route('portfolios.destroy', $portfolio))
            ->assertNotFound();

        $this->assertDatabaseHas('portfolios', ['id' => $portfolio->id]);
    }

    public function test_unpublished_portfolio_is_not_publicly_accessible(): void
    {
        $portfolio = $this->createPortfolio(User::factory()->create());

        $this->get(route('portfolios.public', $portfolio->slug))
            ->assertNotFound();
    }

    private function createPortfolio(User $user, array $overrides = []): Portfolio
    {
        return Portfolio::query()->create(array_merge([
            'user_id' => $user->id,
            'full_name' => 'Avery Student',
            'email' => 'avery@example.com',
            'slug' => 'avery-student-'.uniqid(),
            'template' => 'simple',
            'education' => [],
            'skills' => [],
            'projects' => [],
            'experience' => [],
            'social_links' => [],
            'is_published' => false,
        ], $overrides));
    }
}
