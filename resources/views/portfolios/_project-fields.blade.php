<article data-project-card class="space-y-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5">
    <div class="flex items-center justify-between gap-3">
        <h3 class="font-bold text-slate-900">Project</h3>
        <button type="button" @click="removeProject($el)" class="rounded-lg px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50 focus-visible:outline-brand">
            Remove project
        </button>
    </div>

    <div>
        <label for="project_title_{{ $index }}" class="mb-1 block text-sm font-semibold">Project title</label>
        <input id="project_title_{{ $index }}" name="projects[{{ $index }}][title]" value="{{ old("projects.$index.title", $project['title'] ?? '') }}" maxlength="150" class="w-full px-3 py-2.5" autocomplete="off">
        @if ($errors->has("projects.$index.title"))
            <p class="mt-1 text-sm text-red-700">{{ $errors->first("projects.$index.title") }}</p>
        @endif
    </div>

    <div class="assistant-field">
        <div class="mb-1 flex flex-wrap items-center justify-between gap-2">
            <label for="project_description_{{ $index }}" class="text-sm font-semibold">Description</label>
            <button type="button" @click="suggestText('project_{{ $index }}', 'projects[{{ $index }}][description]')" class="rounded-lg border border-brand/30 bg-white px-3 py-1.5 text-xs font-bold text-brand hover:bg-brand-light">Improve</button>
        </div>
        <textarea id="project_description_{{ $index }}" name="projects[{{ $index }}][description]" rows="3" maxlength="2000" class="w-full px-3 py-2.5">{{ old("projects.$index.description", $project['description'] ?? '') }}</textarea>
        @if ($errors->has("projects.$index.description"))
            <p class="mt-1 text-sm text-red-700">{{ $errors->first("projects.$index.description") }}</p>
        @endif
        <div x-cloak x-show="suggestions['project_{{ $index }}']" class="mt-2 rounded-xl border border-brand/20 bg-brand-light p-3 text-sm">
            <p x-text="suggestions['project_{{ $index }}']?.error || suggestions['project_{{ $index }}']?.text" class="whitespace-pre-line text-slate-800"></p>
            <div x-show="suggestions['project_{{ $index }}']?.text" class="mt-2 flex gap-2">
                <button type="button" @click="acceptSuggestion('project_{{ $index }}')" class="rounded-lg bg-brand px-3 py-1.5 text-xs font-bold text-white hover:bg-brand-hover">Accept</button>
                <button type="button" @click="dismissSuggestion('project_{{ $index }}')" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold">Dismiss</button>
            </div>
        </div>
    </div>

    <div>
        <label for="project_tech_{{ $index }}" class="mb-1 block text-sm font-semibold">Tools or technologies</label>
        <input id="project_tech_{{ $index }}" name="projects[{{ $index }}][tech_stack]" value="{{ old("projects.$index.tech_stack", $project['tech_stack'] ?? '') }}" maxlength="300" class="w-full px-3 py-2.5" placeholder="Laravel, PostgreSQL, Tailwind CSS">
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="project_github_{{ $index }}" class="mb-1 block text-sm font-semibold">GitHub URL</label>
            <input id="project_github_{{ $index }}" type="url" name="projects[{{ $index }}][github_url]" value="{{ old("projects.$index.github_url", $project['github_url'] ?? '') }}" maxlength="500" class="w-full px-3 py-2.5" placeholder="https://github.com/...">
            @if ($errors->has("projects.$index.github_url"))
                <p class="mt-1 text-sm text-red-700">{{ $errors->first("projects.$index.github_url") }}</p>
            @endif
        </div>
        <div>
            <label for="project_demo_{{ $index }}" class="mb-1 block text-sm font-semibold">Live demo URL</label>
            <input id="project_demo_{{ $index }}" type="url" name="projects[{{ $index }}][live_demo_url]" value="{{ old("projects.$index.live_demo_url", $project['live_demo_url'] ?? '') }}" maxlength="500" class="w-full px-3 py-2.5" placeholder="https://...">
            @if ($errors->has("projects.$index.live_demo_url"))
                <p class="mt-1 text-sm text-red-700">{{ $errors->first("projects.$index.live_demo_url") }}</p>
            @endif
        </div>
    </div>

    <div>
        <label for="project_screenshot_{{ $index }}" class="mb-1 block text-sm font-semibold">Screenshot (optional; JPG, PNG, or WebP up to 2 MB)</label>
        <input id="project_screenshot_{{ $index }}" type="file" name="projects[{{ $index }}][screenshot]" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" @change="previewProjectImage($event, 'project-screenshot-preview-{{ $index }}')" class="block w-full text-sm">
        <p id="project-screenshot-preview-{{ $index }}-error" role="alert" class="mt-1 text-sm text-red-700"></p>
        @if ($errors->has("projects.$index.screenshot"))
            <p class="mt-1 text-sm text-red-700">{{ $errors->first("projects.$index.screenshot") }}</p>
        @endif
        <div class="mt-3 flex flex-wrap items-center gap-3">
            <img id="project-screenshot-preview-{{ $index }}" @class(['hidden' => empty($project['screenshot'])]) @if (!empty($project['screenshot'])) src="{{ $portfolio->mediaUrl($project['screenshot']) }}" @endif alt="Project screenshot preview" class="h-24 w-36 rounded-lg border border-slate-200 object-cover">
            @if (!empty($project['screenshot']))
                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="projects[{{ $index }}][remove_screenshot]" value="1" @checked(old("projects.$index.remove_screenshot"))>
                    Remove saved screenshot
                </label>
                <p class="text-xs text-slate-500">Upload another image to replace it.</p>
            @endif
        </div>
    </div>
</article>
