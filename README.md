# Bank Service SOA Implementation

## Overview
This project implements a Service-Oriented Architecture (SOA) for a bank service system. The system consists of multiple microservices that handle different aspects of bank operations, including customer inquiries, support tickets, and user feedback.

## Project Structure
```
TugasUTSKel8/
├── config/
│   ├── db_feedbacks.php
│   ├── db_inquiries.php
│   └── db_tickets.php
├── services/
│   ├── customer_inquiries.php
│   ├── support_tickets.php
│   └── user_feedbacks.php
├── index.php
├── login.php
├── logout.php
└── session_check.php
```

## Services

### 1. Customer Inquiries Service (`customer_inquiries.php`)
- **Purpose**: Handles customer inquiries and questions
- **Operations**:
  - GET: Retrieve all inquiries or a specific inquiry by ID
  - POST: Create a new inquiry
  - PUT: Update an existing inquiry
  - DELETE: Remove an inquiry

### 2. Support Tickets Service (`support_tickets.php`)
- **Purpose**: Manages support tickets for customer issues
- **Operations**:
  - GET: View all tickets or a specific ticket
  - POST: Create a new support ticket
  - PUT: Update ticket status and information
  - DELETE: Remove a ticket

### 3. User Feedbacks Service (`user_feedbacks.php`)
- **Purpose**: Handles customer feedback and ratings
- **Operations**:
  - GET: View all feedbacks or a specific feedback
  - POST: Submit new feedback
  - PUT: Update existing feedback
  - DELETE: Remove feedback

## Database Structure

### 1. Customer Inquiries Database (`db_inquiries`)
- Stores customer inquiries and questions
- Tables:
  - `pertanyaan`: Customer inquiries
  - `users`: User authentication and roles

### 2. Support Tickets Database (`db_tickets`)
- Manages support tickets
- Tables:
  - `tickets`: Support ticket information
  - `users`: User authentication and roles

### 3. Feedback Database (`db_feedbacks`)
- Stores customer feedback
- Tables:
  - `feedbacks`: Customer feedback and ratings
  - `users`: User authentication and roles

## Authentication Flow
1. User logs in through `login.php`
2. Session is created and stored
3. `session_check.php` validates user session for each request
4. User logs out through `logout.php`

## Service Flow

### Customer Inquiry Flow
1. Customer submits an inquiry through the inquiries service
2. Inquiry is stored in the inquiries database
3. Support staff can view and respond to inquiries

### Support Ticket Flow
1. Support staff creates a ticket based on customer inquiry
2. Ticket is stored in the tickets database
3. Ticket status can be updated as the issue is resolved

### Feedback Flow
1. Customer submits feedback after ticket resolution
2. Feedback is stored in the feedback database
3. System administrators can view and manage feedback

## API Endpoints

### Customer Inquiries
- `GET /services/customer_inquiries.php` - List all inquiries
- `GET /services/customer_inquiries.php?id={id}` - Get specific inquiry
- `POST /services/customer_inquiries.php` - Create new inquiry
- `PUT /services/customer_inquiries.php` - Update inquiry
- `DELETE /services/customer_inquiries.php` - Delete inquiry

### Support Tickets
- `GET /services/support_tickets.php` - List all tickets
- `GET /services/support_tickets.php?id={id}` - Get specific ticket
- `POST /services/support_tickets.php` - Create new ticket
- `PUT /services/support_tickets.php` - Update ticket
- `DELETE /services/support_tickets.php` - Delete ticket

### User Feedbacks
- `GET /services/user_feedbacks.php` - List all feedbacks
- `GET /services/user_feedbacks.php?id={id}` - Get specific feedback
- `POST /services/user_feedbacks.php` - Create new feedback
- `PUT /services/user_feedbacks.php` - Update feedback
- `DELETE /services/user_feedbacks.php` - Delete feedback

## Security Features
- Session-based authentication
- Role-based access control (Admin, Support Staff, Customer)
- Input validation and sanitization
- Secure database connections

## Setup Instructions
1. Configure database connections in the `config/` directory
2. Set up the required databases and tables
3. Configure web server to point to the project directory
4. Ensure PHP session handling is properly configured
5. Set appropriate file permissions

## Dependencies
- PHP 7.4 or higher
- MySQL/MariaDB
- Web server (Apache/Nginx)
- PDO PHP Extension
- Session PHP Extension

## Error Handling
- Each service implements proper error handling
- HTTP status codes are used appropriately
- Error messages are logged and returned in JSON format
- Database errors are caught and handled gracefully 