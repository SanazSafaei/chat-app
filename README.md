# Chat Application

A simple chat application built with **Slim Framework 4**, using an **SQLite database** and **Symfony Cache** for caching, designed with a **layered architecture**. This ensures better code organization and maintainability.

## Features
- **User Authentication**: Secure login and registration functionality with JWT token.
- **Private Messaging**: Users can send direct messages to each other.
- **Group Messaging**: Allows multiple users to communicate in a shared chat.
- **Media Uploads**: Users can share images and files within conversations.

---

## Database Design & API Routes

The database is designed for a small-scale chat application supporting **10,000 users/year**, with each user sending around **5 messages per day**.

For larger applications with high loads, it’s recommended to:
- Separate **private messages** and **group messages** into different tables for better organization and performance.
- Use **sharded databases** to distribute data across multiple servers, improving scalability and reducing bottlenecks.
- Implement **read and write database replication** to distribute query loads effectively and ensure high availability.
- Introduce **event-driven architecture** with message queues (e.g., **RabbitMQ** or **Kafka**) to handle high-throughput message processing asynchronously.
- Utilize **rate limiting** and **API throttling** to prevent abuse and improve system stability.

![Database Diagram](ReadMeContent/chat-app-diagram.png)

The API routes follow a RESTful design, ensuring easy integration with frontend clients.

---

## API Documentation
To explore the API endpoints, you can import the Postman collection using this file:
```
./ReadMeContent/Chat-app.postman_collection.json
```
This collection includes predefined requests for testing authentication, messaging, and media uploads.

---

## Installation
### Run Locally
To set up the project in a local development environment, run the following commands:
```bash
cd Chat-Application
composer install
```

### Run with Docker
To deploy the application using Docker, execute:
```bash
cd Chat-Application
docker-compose up -d
```
Once the application is running, open `http://localhost:8080` in your browser to access it.

---

## Running Tests
Currently, this application includes **53 test cases** with **84 assertions**, covering all API test scenarios and core functionalities. It includes both **successful** and **failure** scenarios, ensuring comprehensive validation of API responses and critical layer functions that drive the core features of the system.

### Test Coverage Report
A detailed test coverage report can be found here:
```
https://github.com/SanazSafaei/chat-app/blob/main/phpunit/html-coverage/index.html
```

The test suite covers both **successful** and **failure** scenarios, ensuring robustness.

![Test Coverage](ReadMeContent/test-coverage-result.png)

### Run Tests
To execute the test suite, navigate to the project directory and run:
```bash
composer test
```
**Expected Output:**
```
Result: OK (53 tests, 84 assertions)
```

By maintaining a high level of test coverage, we ensure the application remains stable and secure as new features are introduced.

---

## Future Improvements
This version of the application is functional but can be improved for better performance and scalability. Here are some recommended enhancements:

- **Real-time Communication**: Implement **WebSockets** instead of HTTP-based messaging to enable instant message delivery and improve the overall user experience.
- **Cloud Storage**: Store media files in **object storage solutions**, such as **AWS S3**, to reduce storage limitations, enhance scalability, and improve availability.
- **Reliable Database**: Upgrade from SQLite to **MySQL** for handling larger user bases and better transaction performance.
- **Improved Caching**: Use **Memcached** or **Redis** for efficient data retrieval, reducing database queries and improving response times.
- **Database Optimization**: Add indexing to frequently accessed tables like `messages` and `users` to speed up query execution and reduce latency.
- **Robust ORM**: Switch to **Doctrine** for more efficient database management, making it easier to handle complex queries and relationships.
- **Admin Panel**: Develop an admin dashboard for user moderation, handling reported messages, and managing system-wide configurations.
- **Notifications**: Implement notification routes and real-time alerts for user activity such as new messages, friend requests, and group invitations.
- **More Tests**: Expand unit and integration tests to cover additional scenarios, ensuring a more reliable and bug-free application.


