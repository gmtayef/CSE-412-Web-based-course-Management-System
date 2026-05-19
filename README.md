# 📚 BOT Learning — Web-Based Course Management System

> **CSE-412 Project** · A full-stack online learning platform where students can browse, enroll in, and track their progress through courses — while instructors manage content and admins oversee the entire system.

---

## 🌟 Overview

**BOT Learning** is a feature-rich web-based course management system built with PHP and MySQL. It supports three distinct user roles — **Student**, **Instructor**, and **Admin** — each with a dedicated dashboard and set of capabilities. Students can discover courses, enroll, watch episode content, take quizzes, submit assignments, and earn certificates upon completion.

---

## ✨ Features

### 👨‍🎓 Student
- Register and log in securely
- Browse and explore available courses
- Add courses to cart and proceed to checkout / enrollment
- Access enrolled courses and watch video episodes
- Take quizzes per course (Java, Python, PHP, C)
- Submit assignments
- Track learning progress
- Generate and download course completion certificates
- View and update personal profile

### 👨‍🏫 Instructor
- Dedicated instructor panel
- Upload and manage course episodes
- View enrolled students
- Manage course content

### 🛡️ Admin
- Full admin panel to manage the platform
- Add, edit, and delete courses
- Add and manage instructors
- View all users and enrollments
- Oversee the entire platform

### 🌐 General
- Home page with featured courses and hero banner
- Course listing page with filtering
- Individual course detail pages (Java, Python, PHP, C)
- FAQ page
- About Us page
- Contact page
- Privacy Policy page

---

## 🛠️ Tech Stack

| Layer      | Technology                          |
|------------|--------------------------------------|
| Backend    | PHP (server-side scripting)          |
| Database   | MySQL (via `course1_db.sql`)         |
| Frontend   | HTML5, CSS3, Tailwind CSS            |
| Icons      | Font Awesome                         |
| Fonts      | Google Fonts (Inter)                 |
| Styling    | Custom CSS + Tailwind utility classes|

---

## 📁 Project Structure

```
├── index.php                  # Landing / Home page
├── config.php                 # Database connection configuration
├── login_form.php             # User login
├── register_form.php          # User registration
├── logout.php                 # Session logout
│
├── course-page.php            # All courses listing
├── course-details.php         # Course detail (generic)
├── course-details-java.php    # Java course detail
├── course-details-python.php  # Python course detail
├── course-details-php.php     # PHP course detail
├── course-list.php            # Filtered course listing
├── filterCourse-page.php      # Course filter logic
├── explore_courses.php        # Explore all courses
│
├── enroll.php                 # Course enrollment
├── cart.php                   # Shopping cart
├── add_to_cart.php            # Add course to cart
├── remove-from-cart.php       # Remove from cart
├── checkout.php               # Checkout process
│
├── episodeList.php            # Episode listing (instructor view)
├── userEpisodeList.php        # Episode listing (student view)
├── episodeUpload.php          # Upload new episode (instructor)
│
├── quiz-java.php              # Java quiz
├── quiz-python.php            # Python quiz
├── quiz-php.php               # PHP quiz
├── quiz-C.php                 # C programming quiz
│
├── assignments.php            # Assignment management
├── generate_certificate.php   # Certificate generation
├── useProfile.php             # User profile
├── my-courses.php             # Student's enrolled courses
│
├── admin.php                  # Admin login/dashboard
├── admin_panel.php            # Admin control panel
├── adminView.php              # Admin view
├── add-course.php             # Add new course (admin)
├── edit-course.php            # Edit course (admin)
├── edit_courses.php           # Edit course logic
├── delete-course.php          # Delete course (admin)
│
├── instructor_panel.php       # Instructor dashboard
├── Instructors.php            # Instructors listing page
├── Instructor1.php            # Single instructor profile
├── add_instructor.php         # Add instructor form
├── add_instructors.php        # Add instructor logic
├── manage_instructors.php     # Manage instructors (admin)
│
├── student_panel.php          # Student dashboard
├── home.php                   # Authenticated home
│
├── about.php                  # About page
├── contact.php                # Contact page
├── FAQ.php                    # FAQ page
├── privacy-policy.php         # Privacy policy
│
├── header.php / footer.php    # Shared layout components
├── course1_db.sql             # MySQL database dump
└── CSS/                       # Stylesheet directory
```

---

## ⚙️ Installation & Setup

### Prerequisites
- PHP >= 7.4
- MySQL >= 5.7
- Apache or Nginx web server (XAMPP / WAMP / LAMP recommended)

### Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/gmtayef/CSE-412-Web-based-course-Management-System.git
   cd CSE-412-Web-based-course-Management-System
   ```

2. **Move files to your server root**
   - For XAMPP: copy the folder to `htdocs/`
   - For WAMP: copy to `www/`

3. **Import the database**
   - Open **phpMyAdmin** (or your MySQL client)
   - Create a new database (e.g., `course1_db`)
   - Import the file `course1_db.sql`

4. **Configure the database connection**
   - Open `config.php`
   - Update your credentials:
     ```php
     $conn = mysqli_connect("localhost", "your_username", "your_password", "course1_db");
     ```

5. **Run the application**
   - Start Apache and MySQL from XAMPP/WAMP
   - Visit `http://localhost/CSE-412-Web-based-course-Management-System/`

---

## 👥 User Roles & Access

| Role       | Login Page         | Dashboard              |
|------------|--------------------|------------------------|
| Student    | `login_form.php`   | `student_panel.php`    |
| Instructor | `login_form.php`   | `instructor_panel.php` |
| Admin      | `admin.php`        | `admin_panel.php`      |

---

## 📸 Key Pages

| Page              | File                  |
|-------------------|-----------------------|
| Home              | `index.php`           |
| All Courses       | `course-page.php`     |
| Enrollment        | `enroll.php`          |
| Cart              | `cart.php`            |
| Quizzes           | `quiz-java.php`, etc. |
| Certificates      | `generate_certificate.php` |
| Admin Panel       | `admin_panel.php`     |
| Instructor Panel  | `instructor_panel.php`|
| Student Dashboard | `student_panel.php`   |

---

## 🔐 Security Notes

- Sessions are used for authentication across all user roles.
- `config.php` contains database credentials — **do not expose this file publicly** and consider adding it to `.gitignore` in production.
- For production use, consider adding password hashing (e.g., `password_hash()`) and input sanitization throughout.

---

## 🗃️ Database

The database dump is included at the root of the project as **`course1_db.sql`**. Import it into MySQL to get the full schema and sample data.

---

## 📖 Course Subjects Available

- ☕ Java Programming
- 🐍 Python Programming
- 🐘 PHP Development
- 💻 C Programming

---

## 🤝 Contributors

| Name        | GitHub                                         |
|-------------|------------------------------------------------|
| gmtayef     | [@gmtayef](https://github.com/gmtayef)         |

---

## 📜 License

This project was developed as part of the **CSE-412: Web Engineering** course. It is intended for academic use.

---

## 📬 Contact

For questions or suggestions, visit the [Contact Page](contact.php) of the application or open an issue on GitHub.

---

> _"Unlock Your Potential" — BOT Learning_
