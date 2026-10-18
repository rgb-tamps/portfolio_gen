import Alpine from 'alpinejs';
import QRCode from 'qrcode';

window.Alpine = Alpine;

window.portfolioWizard = function (initialStep = 1, projectCount = 1, selectedTemplate = 'simple', profilePicture = '', hasExistingPicture = false) {
    return {
        step: Math.min(6, Math.max(1, Number(initialStep) || 1)),
        projectCount,
        selectedTemplate,
        profilePicture,
        hasExistingPicture,
        removeProfilePicture: false,
        suggestions: {},
        preview: {},
        quality: { score: 0, missing: [], invalidLinks: [], incompleteContact: [] },
        steps: [
            { label: 'Personal', id: 1 },
            { label: 'Education', id: 2 },
            { label: 'Skills', id: 3 },
            { label: 'Projects', id: 4 },
            { label: 'Experience', id: 5 },
            { label: 'Links', id: 6 },
        ],
        init() {
            this.refreshPreview();
            this.$el.addEventListener('input', () => this.refreshPreview());
            this.$el.addEventListener('change', () => this.refreshPreview());
        },
        value(name) {
            return this.$el.querySelector(`[name="${CSS.escape(name)}"]`)?.value?.trim() || '';
        },
        lines(name) {
            return this.value(name).split(/\r?\n/).map(value => value.trim()).filter(Boolean);
        },
        refreshPreview() {
            const projectCards = [...this.$el.querySelectorAll('[data-project-card]')];
            const projects = projectCards.map(card => {
                const read = key => card.querySelector(`[name$="[${key}]"]`)?.value?.trim() || '';
                return {
                    title: read('title'),
                    description: read('description'),
                    techStack: read('tech_stack'),
                    githubUrl: read('github_url'),
                    liveDemoUrl: read('live_demo_url'),
                };
            }).filter(project => project.title);

            this.projectCount = projectCards.length;
            this.preview = {
                name: this.value('full_name') || 'Your Name',
                email: this.value('email'),
                about: this.value('about_me') || 'Your introduction will appear here as you write it.',
                skills: this.lines('skills_text').slice(0, 8),
                education: this.lines('education_text').slice(0, 2),
                experience: this.lines('experience_text').slice(0, 2),
                links: this.lines('social_links_text').slice(0, 3),
                projects: projects.slice(0, 2),
                initials: this.initials(this.value('full_name') || 'Your Name'),
            };

            const required = [
                this.value('full_name'),
                /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value('email')),
                this.value('about_me'),
                this.lines('education_text').length,
                this.lines('skills_text').length,
                projects.length,
                this.lines('experience_text').length,
                this.lines('social_links_text').length,
            ];
            const completed = required.filter(value => Boolean(value)).length;
            const urls = [
                ...this.lines('social_links_text'),
                ...projects.flatMap(project => [project.githubUrl, project.liveDemoUrl].filter(Boolean)),
            ];
            const validUrl = value => {
                try {
                    return ['http:', 'https:'].includes(new URL(value).protocol);
                } catch {
                    return false;
                }
            };

            this.quality = {
                score: Math.round((completed / required.length) * 100),
                missing: [
                    !this.value('about_me') && 'About Me',
                    !this.lines('education_text').length && 'Education',
                    !this.lines('skills_text').length && 'Skills',
                    !projects.length && 'Projects',
                    !this.lines('experience_text').length && 'Experience',
                    !this.lines('social_links_text').length && 'Social or website links',
                ].filter(Boolean),
                invalidLinks: urls.filter(url => !validUrl(url)),
                incompleteContact: [
                    !this.value('email') && 'Email',
                    !this.value('contact_number') && 'Contact number',
                    !this.value('address') && 'Location',
                ].filter(Boolean),
            };
        },
        initials(name) {
            return name.trim().split(/\s+/).slice(0, 2).map(part => part[0] || '').join('').toUpperCase();
        },
        next() { if (this.step < 6) this.step++; },
        back() { if (this.step > 1) this.step--; },
        goTo(value) { this.step = Math.min(6, Math.max(1, Number(value) || 1)); },
        previewImage(event, targetId) {
            const file = event.target.files?.[0];
            if (!file) return;
            const error = document.getElementById(`${targetId}-error`);
            const allowed = ['image/jpeg', 'image/png', 'image/webp'];
            if (!allowed.includes(file.type)) {
                if (error) error.textContent = 'Choose a JPG, PNG, or WEBP image.';
                event.target.value = '';
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                if (error) error.textContent = 'The image must be 2 MB or smaller.';
                event.target.value = '';
                return;
            }
            if (error) error.textContent = '';
            this.removeProfilePicture = false;
            const removeInput = this.$el.querySelector('[name="remove_profile_picture"]');
            if (removeInput) removeInput.checked = false;
            this.profilePicture = URL.createObjectURL(file);
            const target = document.getElementById(targetId);
            if (target) {
                target.src = this.profilePicture;
                target.classList.remove('hidden');
            }
            this.refreshPreview();
        },
        clearProfilePicture() {
            const input = this.$el.querySelector('[name="profile_picture"]');
            if (input) input.value = '';
            this.profilePicture = '';
            this.removeProfilePicture = this.hasExistingPicture;
            this.refreshPreview();
        },
        previewProjectImage(event, targetId) {
            const file = event.target.files?.[0];
            if (!file) return;
            const error = document.getElementById(`${targetId}-error`);
            const allowed = ['image/jpeg', 'image/png', 'image/webp'];

            if (!allowed.includes(file.type)) {
                if (error) error.textContent = 'Choose a JPG, PNG, or WEBP image.';
                event.target.value = '';
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                if (error) error.textContent = 'The image must be 2 MB or smaller.';
                event.target.value = '';
                return;
            }

            if (error) error.textContent = '';
            const target = document.getElementById(targetId);
            if (target) {
                target.src = URL.createObjectURL(file);
                target.classList.remove('hidden');
            }
        },
        suggestText(key, targetName) {
            const input = this.$el.querySelector(`[name="${CSS.escape(targetName)}"]`);
            if (!input) return;
            const raw = input.value.trim();
            if (!raw) {
                this.suggestions[key] = { error: 'Add some text first, then try an improvement.' };
                return;
            }

            let suggestion = raw.replace(/\s+/g, ' ').trim();
            if (key === 'skills') {
                suggestion = [...new Set(raw.split(/[,\r\n]+/).map(skill => skill.trim()).filter(Boolean))].join('\n');
            } else {
                suggestion = suggestion.charAt(0).toLocaleUpperCase() + suggestion.slice(1);
                if (!/[.!?]$/.test(suggestion)) suggestion += '.';
            }

            this.suggestions[key] = { targetName, text: suggestion };
        },
        acceptSuggestion(key) {
            const suggestion = this.suggestions[key];
            if (!suggestion?.targetName) return;
            const input = this.$el.querySelector(`[name="${CSS.escape(suggestion.targetName)}"]`);
            if (input) {
                input.value = suggestion.text;
                input.dispatchEvent(new Event('input', { bubbles: true }));
            }
            delete this.suggestions[key];
        },
        dismissSuggestion(key) {
            delete this.suggestions[key];
        },
        addProject() {
            const container = document.getElementById('project-list');
            if (!container) return;
            if (this.projectCount >= 30) return;
            const index = Number(container.dataset.nextIndex || 0);
            container.dataset.nextIndex = String(index + 1);
            const template = document.getElementById('project-template');
            if (!template) return;
            const html = template.innerHTML.replaceAll('__INDEX__', String(index));
            container.insertAdjacentHTML('beforeend', html);
            window.Alpine.initTree(container.lastElementChild);
            this.refreshPreview();
        },
        removeProject(button) {
            button.closest('[data-project-card]')?.remove();
            this.refreshPreview();
        },
        addLineItem(containerId, name, placeholder) {
            const container = document.getElementById(containerId);
            if (!container) return;
            const index = Number(container.dataset.nextIndex || container.children.length);
            container.dataset.nextIndex = String(index + 1);
            const wrapper = document.createElement('div');
            wrapper.className = 'flex gap-2';
            const input = document.createElement('input');
            input.name = `${name}[${index}]`;
            input.placeholder = placeholder;
            input.className = 'w-full rounded-lg border-slate-300';
            input.setAttribute('aria-label', placeholder);
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.textContent = 'Remove';
            remove.className = 'rounded-lg border px-3 text-sm font-semibold text-red-600';
            remove.onclick = () => wrapper.remove();
            wrapper.append(input, remove);
            container.append(wrapper);
        }
    };
};

function initializePortfolioQrs() {
    document.querySelectorAll('[data-portfolio-qr]').forEach(container => {
        const canvas = container.querySelector('[data-qr-canvas]');
        const button = container.querySelector('[data-qr-download]');
        const status = container.querySelector('[data-qr-status]');
        const url = container.dataset.qrUrl;

        if (!canvas || !button || !url) return;

        QRCode.toCanvas(canvas, url, {
            width: 192,
            margin: 2,
            color: { dark: '#111827', light: '#ffffff' },
        }).then(() => {
            button.disabled = false;
            if (status) status.textContent = 'QR code ready to download.';
        }).catch(() => {
            if (status) status.textContent = 'QR code could not be created. You can still copy the public link.';
        });

        button.addEventListener('click', () => {
            const download = document.createElement('a');
            download.download = container.dataset.qrFilename || 'portfolio-qr.png';
            download.href = canvas.toDataURL('image/png');
            download.click();
        });
    });
}

Alpine.start();

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializePortfolioQrs, { once: true });
} else {
    initializePortfolioQrs();
}
