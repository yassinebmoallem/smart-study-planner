<div align="center">

---

> 🇫🇷 **Version française courte**
>
> **Smart Study Planner** est une application web de planification d'études intelligente, développée en PHP/MySQL. Elle génère automatiquement un planning personnalisé par matière, adapte les sessions selon la difficulté de chaque cours, et permet de suivre sa progression en temps réel — le tout dans une interface calme et confortable, pensée pour réduire le stress des révisions.

---

# 📚 Smart Study Planner

**Your intelligent, stress-free study companion.**

A full-stack PHP/MySQL web application that automatically generates a personalized study schedule based on your subjects, exam dates, difficulty levels, and available hours — with real-time progress tracking, an interactive calendar, and visual statistics.

[![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![JavaScript](https://img.shields.io/badge/JavaScript-Vanilla-F7DF1E?style=flat-square&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![Chart.js](https://img.shields.io/badge/Chart.js-4.4-FF6384?style=flat-square&logo=chartdotjs&logoColor=white)](https://www.chartjs.org/)
[![License](https://img.shields.io/badge/license-MIT-green?style=flat-square)](LICENSE)
[![Status](https://img.shields.io/badge/status-active-brightgreen?style=flat-square)]()

</div>

---

## 📋 Table of Contents

- [The Problem & Solution](#-the-problem--solution)
- [Key Features](#-key-features)
- [Tech Stack](#-tech-stack)
- [Screenshots](#-screenshots)
- [Installation](#-installation)
- [Project Structure](#-project-structure)
- [Planning Algorithm](#-planning-algorithm)
- [Database Schema](#-database-schema)
- [Future Improvements](#-future-improvements)
- [Author](#-author)

---

## 💡 The Problem & Solution

### The Problem

Most students face the same cycle every exam season: procrastination, last-minute cramming, anxiety, and poor results. Existing tools like calendars or generic to-do apps don't understand the academic context — they don't know that *Thermodynamics* requires twice the effort of *English Literature*, or that you only have 14 days left before your finals.

### The Solution

**Smart Study Planner** takes your subjects, your exam dates, and your difficulty assessment as input, and automatically builds a day-by-day study schedule tailored to you. It adapts session lengths based on how hard the subject is, tracks your real progress, and lets you regenerate your plan at any time if you fall behind — turning chaotic revision into a calm, structured process.

---

## ✨ Key Features

| Feature | Description |
|---|---|
| 🔐 **Secure Authentication** | Registration & login with PHP `password_hash()` + session management |
| 📖 **Subject Management** | Add, edit, and delete subjects with exam date, difficulty level, and hour estimates |
| 🤖 **Auto Plan Generation** | Rule-based algorithm generates a session-by-session schedule on subject creation |
| ✅ **Session Tracking** | Check off completed sessions via AJAX without page reload; progress updates live |
| 🔁 **Smart Regeneration** | Recalculate the remaining schedule based on actual progress at any time |
| 📅 **Interactive Calendar** | Monthly calendar view displaying all study sessions and exam dates at a glance |
| 📊 **Statistics Dashboard** | Daily activity chart (30 days), weekly activity (8 weeks), consecutive-day streak, global completion rate |
| 💬 **Motivational Quotes** | Rotating daily quotes to maintain focus and positive study mindset |
| 📱 **Responsive Design** | Fully usable on desktop, tablet, and mobile |
| 🎨 **Calm UI** | Soft green palette designed to reduce visual stress during long study sessions |

---

## 🛠 Tech Stack

### Backend
| Technology | Role |
|---|---|
| **PHP 8.0+** | Server-side logic, routing, session handling |
| **MySQLi** | Database queries and connection management |
| **MySQL 5.7+** | Relational data storage (users, subjects, study sessions) |

### Frontend
| Technology | Role |
|---|---|
| **HTML5** | Semantic page structure |
| **CSS3** (custom) | Full design system with CSS variables, flexbox, grid |
| **JavaScript** (Vanilla) | AJAX calls, live UI updates, form preview |
| **Chart.js 4.4** | Interactive bar charts for statistics page |
| **Plus Jakarta Sans** | Google Font — clean, modern, readable at all sizes |

---

## 📸 Screenshots

> Place your screenshot images in a `screenshots/` folder at the project root.

| Page | Description |
|---|---|
| `screenshots/login.png` | Login & registration page |
| `screenshots/dashboard.png` | Main dashboard with today's sessions and subject cards |
| `screenshots/plan.png` | Detailed weekly study plan for a subject |
| `screenshots/calendar.png` | Monthly calendar with sessions and exam markers |
| `screenshots/stats.png` | Statistics page with charts and streak counter |
| `screenshots/add-subject.png` | Subject creation form with live plan preview |

---

## 🚀 Installation

### Prerequisites

- PHP 8.0 or higher
- MySQL 5.7 or higher (or MariaDB 10.3+)
- A local web server: **XAMPP**, **Laragon**, **WAMP**, or PHP's built-in server

---

### Option 1 — PHP Built-in Server (quickest)

```bash
# Step 1 — Clone or copy the project folder
git clone https://github.com/your-username/smart-study-planner.git
cd smart-study-planner

# Step 2 — Create the database
# Open MySQL and run:
mysql -u root -p < database.sql

# Step 3 — Configure database credentials
# Edit connexion.php and set your MySQL host, user, password, and DB name

# Step 4 — Start the server
php -S localhost:8080

# Step 5 — Open your browser
# Go to: http://localhost:8080
```

---

### Option 2 — XAMPP / WAMP / Laragon

```
1. Copy the project folder into:
   - XAMPP  →  C:/xampp/htdocs/smart-study-planner/
   - WAMP   →  C:/wamp64/www/smart-study-planner/
   - Laragon → C:/laragon/www/smart-study-planner/

2. Start Apache and MySQL from your control panel.

3. Create the database:
   Open phpMyAdmin → New Database → name it "smart_study"
   → Import → select database.sql → Go

4. Open your browser at:
   http://localhost/smart-study-planner/
```

---

### Database Configuration

Open `connexion.php` and update these lines:

```php
$host   = "localhost";
$dbname = "smart_study";
$user   = "root";
$pass   = "";          // your MySQL password
```

---

## 📁 Project Structure

```
smart-study-planner-v8/
│
├── index.php              ← Landing page: login + registration tabs
├── dashboard.php          ← Main hub: stats, today's sessions, subject cards
├── add_subject.php        ← Create or edit a subject (triggers plan generation)
├── plan.php               ← Detailed weekly session view for one subject
├── calendar.php           ← Full monthly calendar with sessions + exam dates
├── stats.php              ← Statistics: charts, streak, global progress
├── regenerate.php         ← Recalculate plan based on current progress
├── delete_subject.php     ← Delete subject + cascade remove all sessions
├── toggle_session.php     ← AJAX endpoint: mark session done / undone
├── logout.php             ← Destroy session and redirect
│
├── auth.php               ← Session guard (redirects if not logged in)
├── connexion.php          ← MySQLi database connection
├── planner.php            ← ⭐ Core planning algorithm
├── _helpers.php           ← Utility functions (formatHours, diffLabel, etc.)
├── _icons.php             ← SVG icon library (inline Feather icons)
├── _sidebar.php           ← Reusable sidebar navigation component
│
├── style.css              ← Full design system (CSS variables, components)
├── script.js              ← Vanilla JS: AJAX, live UI updates, animations
│
└── database.sql           ← MySQL schema: utilisateurs, matieres, sessions_etude
```

---

## 🤖 Planning Algorithm

The planning engine lives in `planner.php`. It uses a **rule-based approach** — deterministic, fast, and fully explainable.

### How It Works — Step by Step

```
Input:
  Subject name, difficulty level, exam date,
  total hours to study, hours already completed

Step 1 — Calculate Days Remaining
  daysLeft = examDate - today  (in days)
  If daysLeft ≤ 0 → abort (exam already passed)

Step 2 — Calculate Remaining Work
  hoursLeft = totalHours - completedHours

Step 3 — Compute Base Daily Hours
  baseDailyHours = hoursLeft / daysLeft

Step 4 — Apply Difficulty Multiplier
  ┌─────────────┬────────────┬─────────────────────────────┐
  │ Difficulty  │ Multiplier │ Effect                      │
  ├─────────────┼────────────┼─────────────────────────────┤
  │ difficile   │  × 1.25   │ Longer sessions, more depth │
  │ moyen       │  × 1.00   │ Standard pacing             │
  │ facile      │  × 0.80   │ Shorter sessions, less load │
  └─────────────┴────────────┴─────────────────────────────┘
  dailyHours = baseDailyHours × multiplier

Step 5 — Cap Session Length
  dailyHours = clamp(dailyHours, min=0.5h, max=6h)
  (Prevents burnout and unrealistic micro-sessions)

Step 6 — Clear Pending Sessions
  DELETE all future (fait = 0) sessions for this subject

Step 7 — Insert Daily Sessions
  For each day from today to examDate:
    sessionHours = min(dailyHours, hoursLeft)
    INSERT session (matière_id, date, hours, fait=0)
    hoursLeft -= sessionHours
    if hoursLeft ≤ 0 → stop

Output:
  N rows in sessions_etude, one per day, covering
  all remaining study hours up to the exam date.
```

### Regeneration

When the student clicks **Regenerate**, the same algorithm runs again but reads the updated `heures_completees` value — so only the **remaining** hours are redistributed across the **remaining** days. This lets the student recover cleanly from days they missed.

---

## 🗄 Database Schema

```sql
-- Users
CREATE TABLE utilisateurs (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nom           VARCHAR(100)  NOT NULL,
    email         VARCHAR(150)  NOT NULL UNIQUE,
    mot_de_passe  VARCHAR(255)  NOT NULL,       -- bcrypt via password_hash()
    cree_le       DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Subjects
CREATE TABLE matieres (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id      INT NOT NULL,
    nom                 VARCHAR(150) NOT NULL,
    difficulte          ENUM('facile','moyen','difficile') NOT NULL DEFAULT 'moyen',
    date_examen         DATE NOT NULL,
    heures_totales      FLOAT NOT NULL DEFAULT 0,
    heures_par_semaine  FLOAT NOT NULL DEFAULT 0,
    heures_completees   FLOAT NOT NULL DEFAULT 0,
    cree_le             DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

-- Study Sessions
CREATE TABLE sessions_etude (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    matiere_id    INT NOT NULL,
    date_session  DATE NOT NULL,
    heures        FLOAT NOT NULL DEFAULT 0,
    fait          TINYINT(1) NOT NULL DEFAULT 0,   -- 0 = pending, 1 = done
    FOREIGN KEY (matiere_id) REFERENCES matieres(id) ON DELETE CASCADE
);
```

**Entity Relationships:**
```
utilisateurs (1) ──── (N) matieres (1) ──── (N) sessions_etude
```

---

## 🔮 Future Improvements

- [ ] **Dark mode** — toggle between calm-light and dark themes
- [ ] **Push / Email reminders** — notify users of upcoming sessions
- [ ] **Export to PDF** — printable weekly study schedule
- [ ] **Pomodoro timer** — built-in focus timer per session
- [ ] **Multi-language support** — Arabic, English, French selector
- [ ] **Mobile app (PWA)** — installable progressive web app for offline use
- [ ] **AI suggestions** — recommend optimal study hours based on past performance patterns
- [ ] **Group study rooms** — share a schedule and study with friends
- [ ] **Google Calendar sync** — export sessions to external calendar

---

## 👤 Author

**Yassine Ben Moallem (Oshino)**
Student Developer — L2 SIG
Based in Sidi Henri, Tunisia

- **Email** : yassine.bmaalem@gmail.com
- **LinkedIn** : [Yassine Ben Maallem](https://www.linkedin.com/in/yassine-ben-maallem-02b87330b/)
- **GitHub** : [@yassinebmoallem](https://github.com/yassinebmoallem)

---

<div align="center">

**⭐ If this project helped you, leave a star — it means a lot!**

*Built with patience, PHP, and too many cups of coffee ☕*

</div>