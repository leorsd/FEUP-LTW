# Project LTW 2023 - Carlink

## Group 02g02

| Name            | Number    | E-Mail                   |
| --------------- | --------- | ------------------------ |
| António Braga   | 201708995 | up201708995@edu.fe.up.pt |
| Arnaldo Lopes   | 202307659 | up202307659@edu.fe.up.pt |
| Leandro Resende | 202306343 | up202306343@edu.fe.up.pt |

## Features

**User:**
- [x] Register a new account.
- [x] Log in and out.
- [x] Edit their profile, including their name, username, password, and email.

**Freelancers:**
- [x] List new services, providing details such as category, pricing, delivery time, and service description, along with images or videos.
- [x] Track and manage their offered services.
- [x] Respond to inquiries from clients regarding their services and provide custom offers if needed.
- [x] Mark services as completed once delivered.

**Clients:**
- [x] Browse services using filters like category, price, and rating.
- [x] Engage with freelancers to ask questions or request custom orders.
- [x] Hire freelancers and proceed to checkout (simulate payment process).
- [x] Leave ratings and reviews for completed services.

**Admins:**
- [x] Elevate a user to admin status.
- [x] Introduce new service categories and other pertinent entities.
- [x] Oversee and ensure the smooth operation of the entire system.

**Extra:**
- [x] Real-time chat between clients and freelancers.
- [x] Favorites system to bookmark services.
- [x] Advanced filter options, including order status, category, price, rating, location, and provider.
- [x] Service images and media gallery.
- [x] Profile avatars and customization.
- [x] Admin panel for user/service/category management.
- [x] Order management for both clients and freelancers.

## ⚙️ Project Setup

### 1. Clone the repository

Clone the project repository to your local machine using the following command:

```bash
git clone git@github.com:FEUP-LTW-2025/ltw-project-ltw02g02.git
cd ltw-project-ltw02g02
```

### 2. Set Up the Database

This project uses an SQLite database. To set up the database, follow these steps:

#### 1. Run the Database Setup Script:

First, create the necessary tables based on the schema defined in `schema.sql` by running the following command:

```bash
php scripts/setup_database.php
```

This will create the `database.db` SQLite file and set up the necessary tables for the application.

#### 2. (Optional) Seed the Database:

If you want to populate the database with test data (e.g., dummy users, services, etc.), you can use the `seed_database.php` script.

To seed the database, run the following command:

```bash
php scripts/seed_database.php
```

This will execute the `seed.sql` file and insert sample data into your database.

### 3. Configure PHP

Make sure your PHP environment is set up correctly:

#### 1. Enable PDO and SQLite Extensions:

Ensure the PDO and SQLite3 extensions are enabled in your `php.ini` file. You can find these extensions in the following lines:

```ini
extension=pdo.so
extension=sqlite3.so
```

#### 2. Start the PHP Built-in Server:

To run the project locally using PHP's built-in web server, navigate to the project directory and execute the following command:

```bash
php -S localhost:9000
```

This will start the web server at http://localhost:9000. You can open this in your browser to access the application.

## Test Credentials

The following credentials are provided for testing purposes (see database/seed.sql):

- admin1/adminpass1
- admin2/adminpass2
- freelancer1/freelancerpass
- user1/userpass
