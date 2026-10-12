/**
 * SnapsTubes Contact Page JavaScript
 * Enhanced functionality for the contact page
 */

class ContactPage {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeAnimations();
        this.setupFormValidation();
        this.setupNotifications();
        this.setupAccessibility();
        console.log('SnapsTubes Contact Page initialized successfully!');
    }

    setupEventListeners() {
        document.addEventListener('DOMContentLoaded', () => {
            this.setupScrollAnimations();
            this.setupFormHandling();
            this.setupWhatsAppButtons();
            this.setupScrollToTop();
            this.setupTypingEffect();
            this.setupAutoSave();
            this.setupKeyboardShortcuts();
        });
    }

    setupScrollAnimations() {
        const cards = document.querySelectorAll('.contact-card');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });

        cards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });
    }

    setupFormHandling() {
        const contactForm = document.getElementById('contactForm');
        const submitBtn = contactForm?.querySelector('button[type="submit"]');
        const loadingOverlay = document.getElementById('loadingOverlay');

        if (!contactForm || !submitBtn) return;

        const originalBtnText = submitBtn.innerHTML;

        contactForm.addEventListener('submit', (e) => {
            if (!this.validateForm(contactForm)) {
                e.preventDefault();
                this.showNotification('Please fix the errors in the form.', 'error');
                return;
            }

            // Add loading state
            submitBtn.classList.add('btn-loading');
            submitBtn.disabled = true;
            loadingOverlay?.classList.add('show');

            // Show success notification
            setTimeout(() => {
                this.showNotification('Message sent successfully! We\'ll get back to you soon.', 'success');
            }, 1000);

            // Reset form after successful submission
            setTimeout(() => {
                this.resetForm(contactForm, submitBtn, originalBtnText, loadingOverlay);
            }, 3000);
        });
    }

    validateForm(form) {
        const inputs = form.querySelectorAll('.contact-input[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!this.validateInput(input)) {
                isValid = false;
            }
        });

        return isValid;
    }

    validateInput(input) {
        const value = input.value.trim();
        const type = input.type;
        const name = input.name;
        let isValid = true;
        let message = '';

        // Remove existing feedback
        const existingFeedback = input.parentElement.querySelector('.invalid-feedback, .valid-feedback');
        existingFeedback?.remove();

        if (input.hasAttribute('required') && !value) {
            isValid = false;
            message = 'This field is required.';
        } else if (type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                message = 'Please enter a valid email address.';
            }
        } else if (name === 'name' && value && value.length < 2) {
            isValid = false;
            message = 'Name must be at least 2 characters long.';
        } else if (name === 'subject' && value && value.length < 5) {
            isValid = false;
            message = 'Subject must be at least 5 characters long.';
        } else if (name === 'message' && value && value.length < 10) {
            isValid = false;
            message = 'Message must be at least 10 characters long.';
        }

        this.applyValidationStyles(input, isValid, message, value);
        return isValid;
    }

    applyValidationStyles(input, isValid, message, value) {
        if (isValid && value) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            this.addFeedback(input, 'valid-feedback', 'Looks good!');
        } else if (!isValid) {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
            this.addFeedback(input, 'invalid-feedback', message);
        } else {
            input.classList.remove('is-valid', 'is-invalid');
        }
    }

    addFeedback(input, className, message) {
        const feedback = document.createElement('div');
        feedback.className = className;
        feedback.textContent = message;
        input.parentElement.appendChild(feedback);
    }

    resetForm(form, submitBtn, originalBtnText, loadingOverlay) {
        submitBtn.classList.remove('btn-loading');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
        loadingOverlay?.classList.remove('show');
    }

    setupFormValidation() {
        const inputs = document.querySelectorAll('.contact-input');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.parentElement.classList.add('input-focused');
                input.classList.remove('is-invalid');
            });

            input.addEventListener('blur', () => {
                input.parentElement.classList.remove('input-focused');
                this.validateInput(input);
            });

            input.addEventListener('input', () => {
                if (input.classList.contains('is-invalid')) {
                    this.validateInput(input);
                }
            });
        });
    }

    setupWhatsAppButtons() {
        const whatsappBtns = document.querySelectorAll('.whatsapp-btn');
        whatsappBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.animateButton(btn);
                const groupType = btn.querySelector('.text-xl')?.textContent || 'WhatsApp';
                this.showNotification(`Redirecting to ${groupType}...`, 'success');
                this.createParticles(btn);
            });

            btn.addEventListener('mouseenter', () => {
                this.createParticles(btn);
            });
        });
    }

    animateButton(button) {
        button.style.transform = 'scale(0.95)';
        setTimeout(() => {
            button.style.transform = 'scale(1)';
        }, 150);
    }

    createParticles(element) {
        for (let i = 0; i < 5; i++) {
            const particle = document.createElement('div');
            particle.style.cssText = `
                position: absolute;
                width: 4px;
                height: 4px;
                background: rgba(255, 255, 255, 0.8);
                border-radius: 50%;
                pointer-events: none;
                z-index: 1000;
            `;

            const rect = element.getBoundingClientRect();
            particle.style.left = (rect.left + Math.random() * rect.width) + 'px';
            particle.style.top = (rect.top + Math.random() * rect.height) + 'px';

            document.body.appendChild(particle);

            particle.animate([
                { transform: 'translateY(0px)', opacity: 1 },
                { transform: 'translateY(-50px)', opacity: 0 }
            ], {
                duration: 1000,
                easing: 'ease-out'
            }).onfinish = () => particle.remove();
        }
    }

    setupScrollToTop() {
        const scrollToTopBtn = document.getElementById('scrollToTop');
        if (!scrollToTopBtn) return;

        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollToTopBtn.classList.add('show');
            } else {
                scrollToTopBtn.classList.remove('show');
            }
        });

        scrollToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    setupTypingEffect() {
        const heading = document.querySelector('h1');
        if (!heading) return;

        const text = heading.textContent;
        heading.textContent = '';
        let i = 0;

        const typeWriter = () => {
            if (i < text.length) {
                heading.textContent += text.charAt(i);
                i++;
                setTimeout(typeWriter, 100);
            }
        };

        setTimeout(typeWriter, 500);
    }

    setupNotifications() {
        // Initialize notification container if it doesn't exist
        if (!document.getElementById('notificationContainer')) {
            const container = document.createElement('div');
            container.id = 'notificationContainer';
            container.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 10000;
            `;
            document.body.appendChild(container);
        }
    }

    showNotification(message, type = 'success') {
        const container = document.getElementById('notificationContainer');
        if (!container) return;

        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} mr-2"></i>
            ${message}
            <button onclick="this.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        `;

        container.appendChild(notification);

        setTimeout(() => notification.classList.add('show'), 100);

        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    setupAutoSave() {
        const contactForm = document.getElementById('contactForm');
        if (!contactForm) return;

        const formInputs = contactForm.querySelectorAll('input, textarea');
        
        formInputs.forEach(input => {
            // Load saved data
            const savedValue = localStorage.getItem(`contact_${input.name}`);
            if (savedValue && !input.value) {
                input.value = savedValue;
            }

            // Save data on input
            input.addEventListener('input', () => {
                localStorage.setItem(`contact_${input.name}`, input.value);
            });
        });

        // Clear saved data on successful submission
        contactForm.addEventListener('submit', () => {
            setTimeout(() => {
                formInputs.forEach(input => {
                    localStorage.removeItem(`contact_${input.name}`);
                });
            }, 2000);
        });
    }

    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + Enter to submit form
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                const contactForm = document.getElementById('contactForm');
                if (document.activeElement?.closest('#contactForm') && contactForm) {
                    e.preventDefault();
                    contactForm.dispatchEvent(new Event('submit'));
                }
            }

            // Escape to close notifications
            if (e.key === 'Escape') {
                document.querySelectorAll('.notification').forEach(notification => {
                    notification.remove();
                });
            }
        });
    }

    setupAccessibility() {
        // Setup focus trap for form
        const contactForm = document.getElementById('contactForm');
        if (!contactForm) return;

        const focusableElements = contactForm.querySelectorAll(
            'input, textarea, button, [tabindex]:not([tabindex="-1"])'
        );
        
        if (focusableElements.length === 0) return;

        const firstFocusable = focusableElements[0];
        const lastFocusable = focusableElements[focusableElements.length - 1];

        contactForm.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                if (e.shiftKey) {
                    if (document.activeElement === firstFocusable) {
                        e.preventDefault();
                        lastFocusable.focus();
                    }
                } else {
                    if (document.activeElement === lastFocusable) {
                        e.preventDefault();
                        firstFocusable.focus();
                    }
                }
            }
        });
    }

    initializeAnimations() {
        // Setup trust indicators animation
        const trustSection = document.querySelector('.mt-16');
        if (trustSection) {
            const trustObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const items = entry.target.querySelectorAll('.text-center');
                        items.forEach((item, index) => {
                            setTimeout(() => {
                                item.style.opacity = '1';
                                item.style.transform = 'translateY(0)';
                            }, index * 200);
                        });
                    }
                });
            });

            const trustItems = trustSection.querySelectorAll('.text-center');
            trustItems.forEach(item => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(30px)';
                item.style.transition = 'all 0.6s ease';
            });

            trustObserver.observe(trustSection);
        }

        // Setup lazy loading for animations
        const lazyAnimations = document.querySelectorAll('.floating-animation');
        const animationObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                } else {
                    entry.target.style.animationPlayState = 'paused';
                }
            });
        });

        lazyAnimations.forEach(element => {
            element.style.animationPlayState = 'paused';
            animationObserver.observe(element);
        });
    }
}

// Initialize the contact page
const contactPage = new ContactPage();

// Error handling
window.addEventListener('error', (e) => {
    console.error('Contact page error:', e.error);
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay?.classList.contains('show')) {
        loadingOverlay.classList.remove('show');
        contactPage.showNotification('Something went wrong. Please try again.', 'error');
    }
});

// Export for potential external use
window.ContactPage = ContactPage;