// API Base URL
const API_BASE_URL = '/EAI/services';

// DOM Elements
document.addEventListener('DOMContentLoaded', () => {
    const navLinks = document.querySelectorAll('.nav-link');
    const contentSections = document.querySelectorAll('.content-section');
    const inquiryForm = document.getElementById('inquiryForm');
    const ticketForm = document.getElementById('ticketForm');
    const feedbackForm = document.getElementById('feedbackForm');
    const ratingStars = document.querySelectorAll('.rating i');

    // Navigation
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetSection = link.getAttribute('data-section');
            
            // Update active states
            navLinks.forEach(l => l.classList.remove('active'));
            link.classList.add('active');
            
            contentSections.forEach(section => {
                section.classList.remove('active');
                if (section.id === targetSection) {
                    section.classList.add('active');
                }
            });
        });
    });

    // Rating System
    let selectedRating = 0;
    ratingStars.forEach(star => {
        star.addEventListener('click', () => {
            const rating = parseInt(star.getAttribute('data-rating'));
            selectedRating = rating;
            
            ratingStars.forEach(s => {
                if (parseInt(s.getAttribute('data-rating')) <= rating) {
                    s.style.color = '#ffc107';
                } else {
                    s.style.color = '#e4e5e9';
                }
            });
        });
    });

    // Customer Inquiries
    if (inquiryForm) {
        inquiryForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const subject = document.getElementById('inquirySubject').value;
            const message = document.getElementById('inquiryMessage').value;
            
            try {
                const response = await fetch(`${API_BASE_URL}/customer_inquiries.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        subject,
                        message
                    })
                });
                
                const data = await response.json();
                if (data.success) {
                    alert('Inquiry berhasil disimpan. Tim kami akan segera menindaklanjuti pertanyaan Anda.');
                    
                    // Tawarkan untuk membuat tiket support
                    if (confirm('Apakah Anda ingin membuat tiket support untuk bantuan lebih lanjut?')) {
                        document.querySelector('[data-section="tickets"]').click();
                    }
                    
                    inquiryForm.reset();
                    loadInquiries();
                } else {
                    alert('Error submitting inquiry: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error submitting inquiry. Please try again.');
            }
        });
    }

    // Support Tickets
    if (ticketForm) {
        ticketForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const type = document.getElementById('ticketType').value;
            const priority = document.getElementById('ticketPriority').value;
            const description = document.getElementById('ticketDescription').value;
            
            try {
                const response = await fetch(`${API_BASE_URL}/support_tickets.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        type,
                        priority,
                        description
                    })
                });
                
                const data = await response.json();
                if (data.success) {
                    alert('Ticket created successfully!');
                    ticketForm.reset();
                    loadTickets();
                    
                    // Tawarkan untuk memberikan feedback
                    if (confirm('Apakah Anda ingin memberikan feedback tentang layanan kami?')) {
                        document.querySelector('[data-section="feedback"]').click();
                    }
                } else {
                    alert('Error creating ticket: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error creating ticket. Please try again.');
            }
        });
    }

    // User Feedback
    if (feedbackForm) {
        feedbackForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (selectedRating === 0) {
                alert('Please select a rating');
                return;
            }
            
            const comment = document.getElementById('feedbackComment').value;
            
            try {
                const response = await fetch(`${API_BASE_URL}/user_feedbacks.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        rating: selectedRating,
                        comment
                    })
                });
                
                const data = await response.json();
                if (data.success) {
                    alert('Feedback submitted successfully!');
                    feedbackForm.reset();
                    selectedRating = 0;
                    ratingStars.forEach(star => star.style.color = '#e4e5e9');
                    loadFeedback();
                } else {
                    alert('Error submitting feedback: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error submitting feedback. Please try again.');
            }
        });
    }

    // Load Functions
    async function loadInquiries() {
        try {
            const response = await fetch(`${API_BASE_URL}/customer_inquiries.php`);
            const data = await response.json();
            
            const inquiriesList = document.getElementById('inquiriesList');
            if (inquiriesList) {
                inquiriesList.innerHTML = '';
                
                data.forEach(inquiry => {
                    const inquiryElement = document.createElement('div');
                    inquiryElement.className = 'list-item';
                    inquiryElement.innerHTML = `
                        <h5>${inquiry.pertanyaan}</h5>
                        <small class="text-muted">Status: <span class="badge ${getStatusBadgeClass(inquiry.status)}">${inquiry.status}</span></small>
                    `;
                    inquiriesList.appendChild(inquiryElement);
                });
            }
        } catch (error) {
            console.error('Error loading inquiries:', error);
        }
    }

    async function loadTickets() {
        try {
            const response = await fetch(`${API_BASE_URL}/support_tickets.php`);
            const data = await response.json();
            
            const ticketsList = document.getElementById('ticketsList');
            if (ticketsList) {
                ticketsList.innerHTML = '';
                
                data.forEach(ticket => {
                    const ticketElement = document.createElement('div');
                    ticketElement.className = 'list-item';
                    ticketElement.innerHTML = `
                        <h5>${ticket.type}</h5>
                        <p>${ticket.description}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Priority: <span class="badge ${getPriorityBadgeClass(ticket.priority)}">${ticket.priority}</span></small>
                            <small class="text-muted">Status: <span class="badge ${getStatusBadgeClass(ticket.status)}">${ticket.status}</span></small>
                        </div>
                    `;
                    ticketsList.appendChild(ticketElement);
                });
            }
        } catch (error) {
            console.error('Error loading tickets:', error);
        }
    }

    async function loadFeedback() {
        try {
            const response = await fetch(`${API_BASE_URL}/user_feedbacks.php`);
            const data = await response.json();
            
            const feedbackList = document.getElementById('feedbackList');
            if (feedbackList) {
                feedbackList.innerHTML = '';
                
                data.forEach(feedback => {
                    const feedbackElement = document.createElement('div');
                    feedbackElement.className = 'list-item';
                    feedbackElement.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="rating">
                                ${generateStars(feedback.rating)}
                            </div>
                            <small class="text-muted">${new Date(feedback.created_at).toLocaleDateString()}</small>
                        </div>
                        <p class="mt-2">${feedback.comment}</p>
                    `;
                    feedbackList.appendChild(feedbackElement);
                });
            }
        } catch (error) {
            console.error('Error loading feedback:', error);
        }
    }

    // Helper Functions
    function getStatusBadgeClass(status) {
        switch (status.toLowerCase()) {
            case 'menunggu':
                return 'badge-warning';
            case 'diproses':
                return 'badge-primary';
            case 'selesai':
                return 'badge-success';
            default:
                return 'badge-light';
        }
    }

    function getPriorityBadgeClass(priority) {
        switch (priority.toLowerCase()) {
            case 'high':
                return 'badge-danger';
            case 'medium':
                return 'badge-warning';
            case 'low':
                return 'badge-success';
            default:
                return 'badge-light';
        }
    }

    function generateStars(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += `<i class="fas fa-star ${i <= rating ? 'text-warning' : 'text-muted'}"></i>`;
        }
        return stars;
    }

    // Initial Load
    loadInquiries();
    loadTickets();
    loadFeedback();
}); 