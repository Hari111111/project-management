import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
});

window.Echo.channel('project-approvals')
    .listen('ProjectApproved', (data) => {
        const notification = `
            <div class="toast show mb-3" role="alert">
                <div class="toast-header bg-success text-white">
                    <strong class="me-auto">Project Approved</strong>
                    <small>${data.timestamp}</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${data.message}
                    <a href="/projects/${data.project_id}" class="alert-link">View Project</a>
                </div>
            </div>
        `;

        const container = document.getElementById('approval-notifications');
        if (container) {
            container.insertAdjacentHTML('afterbegin', notification);
        }
    });
