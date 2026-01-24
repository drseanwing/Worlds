# Worlds

A lightweight worldbuilding and RPG campaign management tool, inspired by Kanka.

## Tech Stack

- **Backend:** PHP 8 + SQLite
- **Frontend:** Alpine.js + Tailwind CSS + HTMX
- **Architecture:** Polymorphic entity model with JSON data fields

## Project Structure

```
Worlds/
├── src/                    # PHP source code
│   ├── Controllers/        # HTTP request handlers
│   ├── Models/            # Data models and business logic
│   ├── Repositories/      # Database access layer
│   ├── Views/             # Template files
│   └── Config/            # Configuration classes
├── public/                # Web-accessible files
│   ├── assets/
│   │   ├── css/          # Compiled CSS files
│   │   └── js/           # JavaScript files
│   └── index.php         # Front controller (to be created)
├── data/                  # Application data (excluded from Git)
│   ├── uploads/          # User-uploaded files
│   └── campaign.db       # SQLite database (created at runtime)
├── database/             # SQL migration files
├── tests/                # Test files
└── documentation/        # Project documentation
    ├── kanka-task-list.md           # Development task list
    └── kanka-lightweight-analysis.md # Architecture analysis
```

## Getting Started

See the [task list](documentation/kanka-task-list.md) for development progress.

## License

TBD