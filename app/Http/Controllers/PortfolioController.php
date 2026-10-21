<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PortfolioController extends Controller
{
    public function home()
    {
        $recentPortfolios = Schema::hasTable('portfolios')
            ? Portfolio::query()
                ->where('is_published', true)
                ->latest('updated_at')
                ->take(6)
                ->get()
            : collect();

        return view('welcome', compact('recentPortfolios'));
    }

    public function index()
    {
        $portfolios = Schema::hasTable('portfolios')
            ? Portfolio::where('user_id', auth()->id())
                ->latest()
                ->get()
            : collect();

        return view('dashboard', compact('portfolios'));
    }

    public function create()
    {
        return view('portfolios.create', [
            'portfolio' => new Portfolio,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $data['user_id'] = auth()->id();
        $data['slug'] = $this->makeSlug($data['full_name']);
        $data['is_published'] = false;

        if (empty($data['template'])) {
            $data['template'] = 'simple';
        }

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request
                ->file('profile_picture')
                ->store('profiles', config('filesystems.portfolio_disk', 'public'));
        }

        unset($data['remove_profile_picture']);

        $portfolio = Portfolio::create($data);

        return redirect()
            ->route('portfolios.templates.select', [
                'portfolio' => $portfolio->id,
            ])
            ->with('success', 'Your portfolio details have been saved.');
    }

    public function edit(Portfolio $portfolio)
    {
        $this->authorizeOwner($portfolio);

        return view('portfolios.edit', compact('portfolio'));
    }

    public function update(Request $request, Portfolio $portfolio)
    {
        $this->authorizeOwner($portfolio);

        $data = $this->validated($request, $portfolio);
        $disk = config('filesystems.portfolio_disk', 'public');
        $pictureToDelete = null;
        $screenshotsToDelete = collect($portfolio->projects ?? [])
            ->pluck('screenshot')
            ->filter()
            ->values();

        // Handle profile picture removal.
        if ($request->boolean('remove_profile_picture')) {
            $pictureToDelete = $portfolio->profile_picture;
            $data['profile_picture'] = null;
        }

        // Handle a replacement profile picture.
        elseif ($request->hasFile('profile_picture')) {
            $pictureToDelete = $portfolio->profile_picture;

            $data['profile_picture'] = $request
                ->file('profile_picture')
                ->store('profiles', $disk);
        }

        unset($data['remove_profile_picture']);

        // Keep the existing slug when the name changes.
        $portfolio->update($data);

        if ($pictureToDelete) {
            Storage::disk($disk)->delete($pictureToDelete);
        }

        $newScreenshots = collect($portfolio->projects ?? [])
            ->pluck('screenshot')
            ->filter();

        $screenshotsToDelete
            ->diff($newScreenshots)
            ->each(fn (string $path) => Storage::disk($disk)->delete($path));

        return redirect()
            ->route('portfolios.preview', $portfolio)
            ->with('success', 'Changes saved successfully.');
    }

    public function templates(Portfolio $portfolio)
    {
        $this->authorizeOwner($portfolio);

        return view('portfolios.templates', compact('portfolio'));
    }

    public function chooseTemplate(Request $request, Portfolio $portfolio)
    {
        abort_unless($portfolio->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'template' => ['required', 'in:simple,modern,creative'],
        ]);

        $portfolio->template = $validated['template'];
        $portfolio->save();

        return redirect()
            ->route('portfolios.preview', $portfolio->id)
            ->with('success', 'Template changed successfully!');
    }

    public function preview(Portfolio $portfolio)
    {
        $this->authorizeOwner($portfolio);

        return view('portfolios.preview', compact('portfolio'));
    }

    public function publish(Portfolio $portfolio)
    {
        $this->authorizeOwner($portfolio);

        $portfolio->update([
            'is_published' => true,
        ]);

        return redirect()
            ->route('portfolios.manage')
            ->with('success', 'Portfolio published successfully.');
    }

    public function unpublish(Portfolio $portfolio)
    {
        $this->authorizeOwner($portfolio);

        $portfolio->update([
            'is_published' => false,
        ]);

        return redirect()
            ->route('portfolios.manage')
            ->with('success', 'Portfolio is now a draft.');
    }

    public function manage()
    {
        $portfolios = Schema::hasTable('portfolios')
            ? Portfolio::where('user_id', auth()->id())
                ->latest()
                ->get()
            : collect();

        return view('portfolios.manage', compact('portfolios'));
    }

    public function destroy(Portfolio $portfolio)
    {
        $this->authorizeOwner($portfolio);

        $disk = config('filesystems.portfolio_disk', 'public');
        $paths = collect([$portfolio->profile_picture])
            ->merge(collect($portfolio->projects ?? [])->pluck('screenshot'))
            ->filter()
            ->unique();

        $portfolio->delete();

        $paths->each(fn (string $path) => Storage::disk($disk)->delete($path));

        return redirect()
            ->route('portfolios.manage')
            ->with('success', 'Portfolio deleted successfully.');
    }

    public function showPublic(string $slug)
    {
        if (! Schema::hasTable('portfolios')) {
            abort(404);
        }

        $portfolio = Portfolio::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('portfolios.public', compact('portfolio'));
    }

    private function validated(
        Request $request,
        ?Portfolio $portfolio = null
    ): array {
        $projectsSubmitted = $request->boolean('projects_present');
        $socialLinksText = $request->input('social_links_text');

        if (is_string($socialLinksText)) {
            $request->merge([
                'social_links' => array_values(array_filter(
                    array_map('trim', preg_split('/\r\n|\r|\n/', $socialLinksText) ?: []),
                    fn (string $url) => $url !== ''
                )),
            ]);
        }

        $data = $request->validate([
            'full_name' => [
                'required',
                'string',
                'max:150',
                'regex:/^[\pL\pM]+(?:[ \'-][\pL\pM]+)*$/u',
            ],
            'email' => ['required', 'email', 'max:255'],

            'contact_number' => [
                'nullable',
                'string',
                'max:30',
                'regex:/^[0-9+\s().-]+$/',
            ],
            'address' => ['nullable', 'string', 'max:1000'],
            'about_me' => ['nullable', 'string', 'max:5000'],

            'profile_picture' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'remove_profile_picture' => ['nullable', 'boolean'],

            'education' => ['nullable', 'array', 'max:20'],
            'education.*' => ['nullable', 'string', 'max:500'],
            'education_text' => ['nullable', 'string', 'max:5000'],

            'skills' => ['nullable', 'array', 'max:40'],
            'skills.*' => ['nullable', 'string', 'max:100'],
            'skills_text' => ['nullable', 'string', 'max:5000'],

            'projects' => ['nullable', 'array', 'max:30'],
            'projects.*.title' => ['nullable', 'string', 'max:150'],
            'projects.*.description' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'projects.*.tech_stack' => [
                'nullable',
                'string',
                'max:300',
            ],
            'projects.*.github_url' => [
                'nullable',
                'url:http,https',
                'max:500',
            ],
            'projects.*.live_demo_url' => [
                'nullable',
                'url:http,https',
                'max:500',
            ],
            'projects.*.screenshot' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
            'projects.*.remove_screenshot' => [
                'nullable',
                'boolean',
            ],

            'experience' => ['nullable', 'array', 'max:20'],
            'experience.*' => ['nullable', 'string', 'max:1000'],
            'experience_text' => ['nullable', 'string', 'max:10000'],

            'social_links' => ['nullable', 'array', 'max:20'],
            'social_links.*' => ['nullable', 'url:http,https', 'max:500'],
            'social_links_text' => ['nullable', 'string', 'max:5000'],

            'template' => [
                'nullable',
                Rule::in(['simple', 'modern', 'creative']),
            ],

            'step' => ['nullable', 'integer', 'between:1,6'],
        ], [
            'email.required' => 'Please enter an email address.',
            'email.email' => 'Please enter a valid email address.',
        ]);

        if ($projectsSubmitted && ! array_key_exists('projects', $data)) {
            $data['projects'] = [];
        }

        // Convert multiline text fields into arrays.
        $textFields = [
            'education_text' => 'education',
            'skills_text' => 'skills',
            'experience_text' => 'experience',
            'social_links_text' => 'social_links',
        ];

        foreach ($textFields as $textField => $arrayField) {
            if (array_key_exists($textField, $data)) {
                $lines = preg_split(
                    '/\r\n|\r|\n/',
                    (string) $data[$textField]
                );

                $data[$arrayField] = array_values(
                    array_filter(
                        array_map('trim', $lines),
                        fn ($value) => $value !== ''
                    )
                );

                unset($data[$textField]);
            }
        }

        // Preserve existing arrays if the corresponding fields
        // were not submitted.
        foreach (['education', 'skills', 'experience', 'social_links'] as $field) {
            if (! array_key_exists($field, $data)) {
                $data[$field] = $portfolio?->{$field} ?? [];
            }

            $data[$field] = array_values(
                array_filter(
                    $data[$field] ?? [],
                    fn ($value) => trim((string) $value) !== ''
                )
            );
        }

        // Process projects and their screenshots.
        $existingProjects = $portfolio?->projects ?? [];

        if (array_key_exists('projects', $data)) {
            $projects = [];

            foreach (($data['projects'] ?? []) as $index => $project) {
                $project = is_array($project) ? $project : [];
                $existing = $existingProjects[$index] ?? [];

                $screenshot = $existing['screenshot'] ?? null;

                if (trim((string) ($project['title'] ?? '')) === '') {
                    continue;
                }

                // Remove the existing screenshot when requested.
                if (! empty($project['remove_screenshot'])) {
                    $screenshot = null;
                }

                // Save a new screenshot if one was uploaded.
                if ($request->hasFile("projects.$index.screenshot")) {
                    $screenshot = $request
                        ->file("projects.$index.screenshot")
                        ->store('project-screenshots', config('filesystems.portfolio_disk', 'public'));
                }

                $projects[] = [
                    'title' => trim($project['title']),
                    'description' => trim(
                        (string) ($project['description'] ?? '')
                    ),
                    'tech_stack' => trim(
                        (string) ($project['tech_stack'] ?? '')
                    ),
                    'github_url' => trim(
                        (string) ($project['github_url'] ?? '')
                    ),
                    'live_demo_url' => trim(
                        (string) ($project['live_demo_url'] ?? '')
                    ),
                    'screenshot' => $screenshot,
                ];
            }

            $data['projects'] = $projects;
        } else {
            $data['projects'] = $existingProjects;
        }

        // Remove form-only fields before saving to the database.
        unset(
            $data['step'],
            $data['remove_profile_picture'],
            $data['education_text'],
            $data['skills_text'],
            $data['experience_text'],
            $data['social_links_text']
        );

        // Do not overwrite an existing template if no template
        // was submitted.
        if (empty($data['template'])) {
            unset($data['template']);
        }

        return $data;
    }

    private function makeSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'portfolio';

        do {
            $slug = $base.'-'.Str::lower(Str::random(7));
        } while (Portfolio::where('slug', $slug)->exists());

        return $slug;
    }

    private function authorizeOwner(Portfolio $portfolio): void
    {
        abort_unless(
            (int) $portfolio->user_id === (int) auth()->id(),
            404
        );
    }
}
