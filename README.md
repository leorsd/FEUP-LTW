# Project LTW 2023 - Carlink

## Group 02g02

| Name            | Number    | E-Mail                   |
| --------------- | --------- | ------------------------ |
| António Braga   | 201708995 | up201708995@edu.fe.up.pt |
| Arnaldo Lopes   | 202307659 | up202307659@edu.fe.up.pt |
| Leandro Resende | 202306343 | up202306343@edu.fe.up.pt |

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
