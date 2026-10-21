@php
    $isEditing = $portfolio->exists;
    $educationText = old('education_text', implode("\n", $portfolio->education ?? []));
    $skillsText = old('skills_text', implode("\n", $portfolio->skills ?? []));
    $experienceText = old('experience_text', implode("\n", $portfolio->experience ?? []));
    $socialLinksText = old('social_links_text', implode("\n", $portfolio->social_links ?? []));
    $projects = old('projects', $portfolio->projects ?? []);

    if (empty($projects)) {
        $projects = [0 => ['title' => '', 'description' => '', 'tech_stack' => '', 'github_url' => '', 'live_demo_url' => '', 'screenshot' => null]];
    }

    $nextProjectIndex = max(array_map('intval', array_keys($projects))) + 1;
    $profilePicture = $portfolio->mediaUrl($portfolio->profile_picture);
    $initialStep = (int) old('step', 1);

    if ($errors->any()) {
        $errorSteps = [];

        foreach ($errors->keys() as $errorKey) {
            $errorSteps[] = match (true) {
                in_array($errorKey, ['full_name', 'email', 'contact_number', 'address', 'about_me', 'profile_picture', 'remove_profile_picture'], true) => 1,
                str_starts_with($errorKey, 'education') => 2,
                str_starts_with($errorKey, 'skills') => 3,
                str_starts_with($errorKey, 'projects') => 4,
                str_starts_with($errorKey, 'experience') => 5,
                str_starts_with($errorKey, 'social_links'), $errorKey === 'template' => 6,
                default => $initialStep,
            };
        }

        $initialStep = min($errorSteps ?: [$initialStep]);
    }
@endphp

<form method="POST"
      action="{{ $isEditing ? route('portfolios.update', $portfolio) : route('portfolios.store') }}"
      enctype="multipart/form-data"
      novalidate
      x-data="portfolioWizard(@js($initialStep), @js(count($projects)), @js(old('template', $portfolio->template ?: 'simple')), @js($profilePicture), @js((bool) $portfolio->profile_picture), @js((bool) old('remove_profile_picture')))"
      x-init="init()"
      class="space-y-6">
    @csrf
    @if ($isEditing)
        @method('PUT')
    @endif

    <input type="hidden" name="step" x-model="step">
    <input type="hidden" name="projects_present" value="1">

    @if ($errors->any())
        <div role="alert" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <p class="font-bold">Please check the highlighted fields.</p>
            <ul class="mt-2 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_21rem]">
        <div class="space-y-6">
            <nav class="grid grid-cols-3 gap-2 sm:grid-cols-6" aria-label="Portfolio form steps">
                @foreach (['Personal', 'Education', 'Skills', 'Projects', 'Experience', 'Links'] as $index => $label)
                    <button type="button"
                            @click="goTo({{ $index + 1 }})"
                            :aria-current="step === {{ $index + 1 }} ? 'step' : null"
                            class="rounded-xl border px-2 py-3 text-xs font-bold transition hover:border-brand sm:text-sm"
                            :class="step === {{ $index + 1 }} ? 'border-brand bg-brand-light text-brand' : 'border-slate-200 bg-white text-slate-600'">
                        <span class="block text-base">{{ $index + 1 }}</span>{{ $label }}
                    </button>
                @endforeach
            </nav>

            <section x-cloak x-show="step === 1" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 sm:p-7">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">Personal information</h2>
                    <p class="mt-1 text-sm text-slate-600">Tell people who you are and how to contact you.</p>
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="full_name" class="mb-1 block text-sm font-semibold">Full name <span aria-hidden="true">*</span></label>
                        <input id="full_name" name="full_name" value="{{ old('full_name', $portfolio->full_name) }}" maxlength="150" required autocomplete="name" class="w-full px-3 py-2.5">
                        @error('full_name') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="mb-1 block text-sm font-semibold">Email <span aria-hidden="true">*</span></label>
                        <input id="email" type="email" name="email" value="{{ old('email', $portfolio->email) }}" maxlength="255" required autocomplete="email" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror class="w-full px-3 py-2.5">
                        @error('email') <p id="email-error" class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="contact_number" class="mb-1 block text-sm font-semibold">Contact number</label>
                        <input id="contact_number" name="contact_number" value="{{ old('contact_number', $portfolio->contact_number) }}" maxlength="30" inputmode="tel" pattern="[0-9+().\s-]+" title="Use digits and common phone-number characters only." autocomplete="tel" class="w-full px-3 py-2.5">
                        @error('contact_number') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="address" class="mb-1 block text-sm font-semibold">Address or location</label>
                        <input id="address" name="address" value="{{ old('address', $portfolio->address) }}" maxlength="1000" autocomplete="address-level2" class="w-full px-3 py-2.5">
                        @error('address') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                    </div>
                    <div class="assistant-field sm:col-span-2">
                        <div class="mb-1 flex flex-wrap items-center justify-between gap-2">
                            <label for="about_me" class="text-sm font-semibold">About Me</label>
                            <button type="button" @click="suggestText('about_me', 'about_me')" class="rounded-lg border border-brand/30 bg-white px-3 py-1.5 text-xs font-bold text-brand hover:bg-brand-light">Improve</button>
                        </div>
                        <textarea id="about_me" name="about_me" rows="4" maxlength="5000" class="w-full px-3 py-2.5" placeholder="Write a short introduction...">{{ old('about_me', $portfolio->about_me) }}</textarea>
                        @error('about_me') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                        <div x-cloak x-show="suggestions.about_me" class="mt-2 rounded-xl border border-brand/20 bg-brand-light p-3 text-sm">
                            <p x-text="suggestions.about_me?.error || suggestions.about_me?.text" class="whitespace-pre-line text-slate-800"></p>
                            <div x-show="suggestions.about_me?.text" class="mt-2 flex gap-2">
                                <button type="button" @click="acceptSuggestion('about_me')" class="rounded-lg bg-brand px-3 py-1.5 text-xs font-bold text-white hover:bg-brand-hover">Accept</button>
                                <button type="button" @click="dismissSuggestion('about_me')" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold">Dismiss</button>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Writing suggestions are created in your browser; your text is not sent to an AI service.</p>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="mb-2 block text-sm font-semibold">Profile picture</span>
                        <div class="flex flex-wrap items-center gap-4">
                            <img id="profile-picture-preview" x-show="profilePicture && !removeProfilePicture" :src="profilePicture" alt="Profile picture preview" class="h-20 w-20 rounded-full border border-slate-200 object-cover">
                            <div x-show="!profilePicture || removeProfilePicture" class="flex h-20 w-20 items-center justify-center rounded-full bg-brand-light text-xl font-extrabold text-brand" x-text="preview.initials"></div>
                            <div class="space-y-2">
                                <label for="profile_picture" class="mb-1 block text-sm font-semibold">Choose or replace picture</label>
                                <input id="profile_picture" type="file" name="profile_picture" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" @change="previewImage($event, 'profile-picture-preview')" class="block w-full max-w-sm text-sm">
                                <button type="button" x-show="profilePicture && !removeProfilePicture" @click="clearProfilePicture()" class="ml-2 rounded-lg px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Remove</button>
                                <p class="text-xs text-slate-500">JPG, PNG, or WebP. Maximum 2 MB.</p>
                            </div>
                        </div>
                        <input type="checkbox" name="remove_profile_picture" value="1" x-model="removeProfilePicture" class="sr-only">
                        <p id="profile-picture-preview-error" role="alert" class="mt-1 text-sm text-red-700"></p>
                        @error('profile_picture') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <section x-cloak x-show="step === 2" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 sm:p-7">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">Education</h2>
                    <p class="mt-1 text-sm text-slate-600">Enter one school, course, or qualification per line.</p>
                </div>
                <label for="education_text" class="block text-sm font-semibold">Education history</label>
                <textarea id="education_text" name="education_text" rows="7" maxlength="5000" class="w-full px-3 py-2.5" placeholder="BS Information Technology, Example College&#10;Senior High School, Example School">{{ $educationText }}</textarea>
                @error('education_text') <p class="text-sm text-red-700">{{ $message }}</p> @enderror
            </section>

            <section x-cloak x-show="step === 3" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 sm:p-7">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">Skills</h2>
                    <p class="mt-1 text-sm text-slate-600">Enter one skill per line or separate skills with commas.</p>
                </div>
                <div class="assistant-field">
                    <div class="mb-1 flex flex-wrap items-center justify-between gap-2">
                        <label for="skills_text" class="text-sm font-semibold">Your skills</label>
                        <button type="button" @click="suggestText('skills', 'skills_text')" class="rounded-lg border border-brand/30 bg-white px-3 py-1.5 text-xs font-bold text-brand hover:bg-brand-light">Improve</button>
                    </div>
                    <textarea id="skills_text" name="skills_text" rows="7" maxlength="5000" class="w-full px-3 py-2.5" placeholder="HTML and CSS&#10;Java&#10;Communication">{{ $skillsText }}</textarea>
                    @error('skills_text') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                    <div x-cloak x-show="suggestions.skills" class="mt-2 rounded-xl border border-brand/20 bg-brand-light p-3 text-sm">
                        <p x-text="suggestions.skills?.error || suggestions.skills?.text" class="whitespace-pre-line text-slate-800"></p>
                        <div x-show="suggestions.skills?.text" class="mt-2 flex gap-2">
                            <button type="button" @click="acceptSuggestion('skills')" class="rounded-lg bg-brand px-3 py-1.5 text-xs font-bold text-white hover:bg-brand-hover">Accept</button>
                            <button type="button" @click="dismissSuggestion('skills')" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold">Dismiss</button>
                        </div>
                    </div>
                </div>
            </section>

            <section x-cloak x-show="step === 4" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 sm:p-7">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">Projects</h2>
                    <p class="mt-1 text-sm text-slate-600">Showcase your work with a description, tech stack, and links.</p>
                </div>
                <div id="project-list" data-next-index="{{ $nextProjectIndex }}" class="space-y-4">
                    @foreach ($projects as $index => $project)
                        @include('portfolios._project-fields', ['index' => $index, 'project' => $project])
                    @endforeach
                </div>
                <template id="project-template">
                    @include('portfolios._project-fields', ['index' => '__INDEX__', 'project' => []])
                </template>
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <button type="button" @click="addProject()" :disabled="projectCount >= 30" class="rounded-xl border border-brand px-4 py-2.5 font-bold text-brand hover:bg-brand-light disabled:cursor-not-allowed disabled:opacity-50">Add project</button>
                    <p class="text-xs text-slate-500">Up to 30 projects; one screenshot per project, max 2 MB each.</p>
                </div>
                @error('projects') <p class="text-sm text-red-700">{{ $message }}</p> @enderror
            </section>

            <section x-cloak x-show="step === 5" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 sm:p-7">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">Work experience</h2>
                    <p class="mt-1 text-sm text-slate-600">Enter one role, job, internship, or volunteer experience per line.</p>
                </div>
                <label for="experience_text" class="block text-sm font-semibold">Experience</label>
                <textarea id="experience_text" name="experience_text" rows="7" maxlength="10000" class="w-full px-3 py-2.5" placeholder="IT Intern, Company Name, 2026&#10;Freelance Web Designer, 2025">{{ $experienceText }}</textarea>
                @error('experience_text') <p class="text-sm text-red-700">{{ $message }}</p> @enderror
            </section>

            <section x-cloak x-show="step === 6" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 sm:p-7">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">Links and template</h2>
                    <p class="mt-1 text-sm text-slate-600">Add one full HTTP or HTTPS URL per line. You can change your template later.</p>
                </div>
                <div>
                    <label for="social_links_text" class="mb-1 block text-sm font-semibold">Social media and website links</label>
                    <textarea id="social_links_text" name="social_links_text" rows="6" maxlength="5000" class="w-full px-3 py-2.5" placeholder="https://github.com/yourname&#10;https://linkedin.com/in/yourname">{{ $socialLinksText }}</textarea>
                    @for ($linkIndex = 0; $linkIndex < 20; $linkIndex++)
                        @error("social_links.$linkIndex") <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                    @endfor
                    @error('social_links') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                    @error('social_links_text') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
                <fieldset>
                    <legend class="mb-2 text-sm font-semibold">Starting template</legend>
                    <div class="grid gap-3 sm:grid-cols-3">
                        @foreach (['simple' => 'Simple', 'modern' => 'Modern', 'creative' => 'Creative'] as $value => $label)
                            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 p-3 text-sm font-semibold hover:border-brand">
                                <input type="radio" name="template" value="{{ $value }}" x-model="selectedTemplate" @checked(old('template', $portfolio->template ?: 'simple') === $value)>
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    @error('template') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                </fieldset>
            </section>

            <div class="flex items-center justify-between gap-3">
                <button type="button" @click="back()" x-show="step > 1" class="rounded-xl border border-slate-300 bg-white px-5 py-3 font-bold text-slate-700 hover:bg-slate-50">Back</button>
                <span class="text-sm text-slate-600">Step <span x-text="step"></span> of 6</span>
                <button type="button" @click="next()" x-show="step < 6" class="ml-auto rounded-xl bg-brand px-5 py-3 font-bold text-white hover:bg-brand-hover">Next step</button>
                <button type="submit" x-show="step === 6" class="ml-auto rounded-xl bg-brand px-5 py-3 font-bold text-white hover:bg-brand-hover">{{ $isEditing ? 'Save changes' : 'Save portfolio and choose template' }}</button>
            </div>
        </div>

        <aside class="space-y-5 self-start lg:sticky lg:top-6">
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" aria-labelledby="draft-preview-title">
                <div class="border-b border-slate-200 p-4">
                    <h2 id="draft-preview-title" class="font-extrabold text-slate-900">Live draft preview</h2>
                    <p class="mt-1 text-xs text-slate-600">Switch designs to preview your current form data.</p>
                    <div class="mt-3 grid grid-cols-3 gap-1" role="group" aria-label="Preview template">
                        @foreach (['simple' => 'Simple', 'modern' => 'Modern', 'creative' => 'Creative'] as $value => $label)
                            <button type="button" @click="selectedTemplate = '{{ $value }}'" :aria-pressed="selectedTemplate === '{{ $value }}'" class="rounded-lg border px-2 py-2 text-xs font-bold" :class="selectedTemplate === '{{ $value }}' ? 'border-brand bg-brand-light text-brand' : 'border-slate-200 text-slate-600'">{{ $label }}</button>
                        @endforeach
                    </div>
                </div>
                <div x-show="selectedTemplate === 'simple'" class="min-h-64 bg-white p-4 text-slate-900">
                    <div class="border-b border-slate-200 pb-3 text-xs font-black">PORTFOLIO<span class="text-brand">.</span></div>
                    <div class="flex items-center gap-3 py-4">
                        <img x-show="profilePicture && !removeProfilePicture" :src="profilePicture" alt="" class="h-12 w-12 rounded-xl object-cover">
                        <div x-show="!profilePicture || removeProfilePicture" class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-light font-bold text-brand" x-text="preview.initials"></div>
                        <h3 x-text="preview.name" class="break-words font-extrabold"></h3>
                    </div>
                    <p x-text="preview.about" class="line-clamp-3 text-xs leading-5 text-slate-600"></p>
                    <div class="mt-3 flex flex-wrap gap-1"><template x-for="skill in preview.skills" :key="skill"><span x-text="skill" class="rounded bg-slate-100 px-2 py-1 text-[10px]"></span></template></div>
                    <template x-for="project in preview.projects" :key="project.title"><div class="mt-3 rounded-lg border border-slate-200 p-2"><p x-text="project.title" class="text-xs font-bold"></p><p x-text="project.description" class="mt-1 line-clamp-2 text-[10px] text-slate-600"></p></div></template>
                </div>
                <div x-cloak x-show="selectedTemplate === 'modern'" class="min-h-64 bg-orange-50 p-4 text-slate-900">
                    <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-widest text-brand"><span>Portfolio</span><span>Selected work</span></div>
                    <div class="mt-4 flex items-center gap-3">
                        <img x-show="profilePicture && !removeProfilePicture" :src="profilePicture" alt="" class="h-14 w-14 rounded-2xl object-cover">
                        <div x-show="!profilePicture || removeProfilePicture" class="flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-light text-lg font-black text-brand" x-text="preview.initials"></div>
                        <div><p class="text-[10px] font-bold uppercase tracking-widest text-brand">Hello, I'm</p><h3 x-text="preview.name" class="break-words text-lg font-black"></h3></div>
                    </div>
                    <p x-text="preview.about" class="mt-3 line-clamp-3 text-xs leading-5 text-slate-600"></p>
                    <div class="mt-3 grid gap-2"><template x-for="project in preview.projects" :key="project.title"><div class="rounded-xl border border-orange-200 bg-white p-2"><p x-text="project.title" class="text-xs font-bold"></p><p x-text="project.techStack" class="mt-1 text-[10px] text-brand"></p></div></template></div>
                </div>
                <div x-cloak x-show="selectedTemplate === 'creative'" class="min-h-64 bg-slate-950 p-4 text-white">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-orange-400">My World<span class="text-brand">.</span></div>
                    <div class="mt-4 flex items-center gap-3">
                        <img x-show="profilePicture && !removeProfilePicture" :src="profilePicture" alt="" class="h-14 w-14 rounded-2xl object-cover">
                        <div x-show="!profilePicture || removeProfilePicture" class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-800 text-lg font-black text-brand" x-text="preview.initials"></div>
                        <h3 x-text="preview.name" class="break-words text-lg font-black uppercase"></h3>
                    </div>
                    <p x-text="preview.about" class="mt-3 line-clamp-3 text-xs leading-5 text-slate-300"></p>
                    <div class="mt-3 space-y-2"><template x-for="project in preview.projects" :key="project.title"><div class="border-l-2 border-brand bg-white/5 p-2"><p x-text="project.title" class="text-xs font-bold"></p><p x-text="project.description" class="mt-1 line-clamp-2 text-[10px] text-slate-300"></p></div></template></div>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-4" aria-labelledby="quality-checker-title">
                <div class="flex items-center justify-between gap-3">
                    <h2 id="quality-checker-title" class="font-extrabold text-slate-900">Portfolio quality</h2>
                    <span class="rounded-full bg-brand-light px-3 py-1 text-sm font-extrabold text-brand"><span x-text="quality.score"></span>%</span>
                </div>
                <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100" role="progressbar" :aria-valuenow="quality.score" aria-valuemin="0" aria-valuemax="100" aria-label="Portfolio completeness">
                    <div class="h-full rounded-full bg-brand transition-all" :style="`width: ${quality.score}%`"></div>
                </div>
                <div class="mt-4 space-y-3 text-xs">
                    <div>
                        <h3 class="font-bold text-slate-800">Missing sections</h3>
                        <p x-show="!quality.missing.length" class="mt-1 text-emerald-700">All main sections have content.</p>
                        <ul x-show="quality.missing.length" class="mt-1 list-inside list-disc text-slate-600"><template x-for="item in quality.missing" :key="item"><li x-text="item"></li></template></ul>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800">Invalid links</h3>
                        <p x-show="!quality.invalidLinks.length" class="mt-1 text-emerald-700">No invalid links detected.</p>
                        <ul x-show="quality.invalidLinks.length" class="mt-1 list-inside list-disc break-all text-red-700"><template x-for="item in quality.invalidLinks" :key="item"><li x-text="item"></li></template></ul>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800">Contact details to consider</h3>
                        <p x-show="!quality.incompleteContact.length" class="mt-1 text-emerald-700">Contact details look complete.</p>
                        <p x-show="quality.incompleteContact.length" x-text="quality.incompleteContact.join(', ')" class="mt-1 text-slate-600"></p>
                    </div>
                </div>
            </section>
        </aside>
    </div>
</form>
