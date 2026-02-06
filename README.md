# HRPMS - Human Resource & Project Management System

This project is a comprehensive Human Resource and Project Management System built on the [Laravel](https://laravel.com) framework. It is designed to manage various aspects of organizational operations, including employee management, payroll, leave requests, inventory, and more.

## Technology Stack

- **Framework:** Laravel 9.x
- **Language:** PHP ^8.0.2
- **Database:** MySQL (Standard Laravel support)
- **Frontend:** Blade Templates, jQuery, Bootstrap
- **Key Libraries:**
    - `yajra/laravel-datatables`: For advanced table interactions.
    - `maatwebsite/excel`: For Excel imports and exports.
    - `barryvdh/laravel-dompdf`: For PDF generation.
    - `rats/zkteco`: For ZKTeco biometric device integration.

## Architecture

The application follows a **Modular Architecture**. Instead of grouping code by type (Controllers, Models), the system is divided into functional modules located in the `modules/` directory.

### Directory Structure

- `app/`: Core application code and shared helpers.
- `modules/`: Contains domain-specific modules (e.g., `Employee`, `Payroll`).
- `config/`: Application configuration files.
- `database/`: Migrations, factories, and seeders.
- `resources/`: Views, assets (JS/CSS), and language files.
- `routes/`: Web and API route definitions.

## Modules

The system is composed of several independent modules, including but not limited to:

- **Employee Management:** `Employee`, `Profile`, `EmployeeExit`, `ProbationaryReview`
- **Attendance & Leave:** `EmployeeAttendance`, `LeaveRequest`, `WorkLog`
- **Payroll & Finance:** `Payroll`, `PaymentSheet`, `AdvanceRequest`, `FundRequest`
- **Procurement & Inventory:** `Inventory`, `PurchaseRequest`, `PurchaseOrder`, `Grn` (Goods Received Note), `Supplier`
- **Travel & Logistics:** `TravelRequest`, `TravelAuthorization`, `VehicleRequest`, `TransportationBill`
- **General Administration:** `MeetingHallBooking`, `Announcement`, `Memo`

## Installation

### Prerequisites

- PHP >= 8.0.2
- Composer
- Node.js & NPM

### Setup Steps

1.  **Clone the repository**

    ```bash
    git clone https://gitlab.com/dryicesolutions/php-application/hrpms.git
    cd hrpms
    ```

2.  **Install PHP Dependencies**

    ```bash
    composer install
    ```

3.  **Install Frontend Dependencies**

    ```bash
    npm install
    npm run dev
    ```

4.  **Environment Configuration**
    Copy the example environment file and configure your database credentials.

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5.  **Database Migration**
    Run migrations to set up the database schema.

    ```bash
    php artisan migrate
    ```

6.  **Serve the Application**
    ```bash
    php artisan serve
    ```

## Testing

For testing purposes, you can access the live testing environment at:
[https://hrpms.dryicesolutions.net/](https://hrpms.dryicesolutions.net/)

## Development Workflow & Contribution

To contribute to this project, please follow these steps:

1.  **Create a Branch**
    - For new features: `feat/feature-name`
    - For bug fixes: `fix/bug-name`

2.  **Push Changes**
    Push your branch to the remote repository.

3.  **Merge Request**
    Create a Merge Request (MR) targeting the `uat` branch.

## Business Logic Documentation

For detailed explanations of specific business rules and logic, please refer to the documents below:

- [Lieu Leave Request Rules](./docs/LieuLeaveRequestRules.md)
- [Work Plan Rules](./docs/WorKPlanRules.md)
- [Project Activity Rules](./docs/ProjectActivityRules.md)

## Documentation & Usage

Each module within the `modules/` directory typically contains its own set of:

- **Controllers:** Handling HTTP requests.
- **Models:** Database interactions.
- **Views:** User interface templates.
- **Migrations:** Database schema changes specific to the module.

For specific feature implementation details, refer to the code within the respective module folder.

## Support

For issues or feature requests, please contact the development team or check the project repository issues section.
