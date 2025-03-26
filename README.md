# Chat Application

This is a chat application built using **Slim Framework 4**. It includes the following features:

- **User Authentication**
- **Private Messaging**
- **Group Messaging**
- **Media Uploads**

## Future Improvements

This is not the final version of the application. To ensure better performance and scalability under high loads, the following enhancements are recommended:

- **Real-time Communication**: Replacing HTTP-based messaging with **WebSockets** for a seamless chat experience.
- **Cloud Storage**: Storing media files on **AWS S3** for better scalability.
- **Reliable Database**: Utilizing **MySQL** instead of the default database.
- **API Caching**: Implementing caching mechanisms to improve response times.
- **Database Optimization**: Adding indexing to frequently accessed tables, such as `messages` and `users`, to enhance query performance.
- **Use a more robust ORM**: Switching to **Doctrine** for better database management.
- **Admin panel**: Add an admin panel for managing users and reported users

 ## Database Schemas and Routes
An Overview of db schemas and routes and todo lists for expanding project:
![Chat Application Logo](ReadMeContent/chat-application-diagram.png)

## Postman Collection
 You can load Postman Collection of application result with this file:
```
./ReadMeContent/Chat-app.postman_collection.json
```

## Installation

To run the application in development, you can use these commands:

```bash
cd Chat-Application
composer install
```

Alternatively, you can use docker-compose to run the app with Docker:
```bash
cd Chat-Application
docker-compose up -d
```
After that, open `http://localhost:8080` in your browser.

## Test Coverage
You can see test coverage result via this file:
```
https://github.com/SanazSafaei/chat-app/blob/main/phpunit/html-coverage/index.html
```
![Chat Application Logo](ReadMeContent/test-coverage-result.png)

Run this command in the application directory to run the test suite

```bash
composer test
```