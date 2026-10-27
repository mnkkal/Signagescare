# Signage Care CRM & Service Operations Management Platform

[![Laravel 11](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP 8.3](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)

A unified web platform for multi-location retail signage assets, breakdown service requests, ticket tracking, preventive maintenance (AMC), proof-of-work before/after evidence, and standard operating procedures (SOP).

---

## Key Modules & Capabilities

1. **Client & Multi-Location Portal**:
   - Multi-tenant retail network hierarchy (e.g. *Lenskart* &rarr; *Dharampeth, Sadar, Hingna, Manish Nagar, Besa*).
   - Scoped visibility: Store managers view only their assigned locations and assets.

2. **Breakdown Support & Service Tickets**:
   - Formatted ticket tracking (`SC-NAG-0048`).
   - Priority levels: `P1` (Critical), `P2` (High), `P3` (Medium), `P4` (Low).
   - Ticket lifecycle: `Open` &rarr; `Assigned` &rarr; `In Progress` &rarr; `Parts Required` &rarr; `Scheduled` &rarr; `Completed` &rarr; `Overdue`.
   - Technician assignment and real-time ETA tracking.

3. **Asset Register & Condition Monitoring**:
   - Detailed specifications: acrylic 3D LED, ACP backlit, glowsign, vertical blade signs, pylon signs.
   - Live condition classification: **Healthy**, **Attention Required**, and **Critical**.

4. **Before & After Photographic Proof**:
   - Technicians upload mandatory photographic evidence (initial defect before service vs. verified illumination after service).

5. **Annual Maintenance Contracts (AMC)**:
   - Quarterly preventive visit schedules, SLA guarantees (24h response / 48h resolution), covered branches list, and renewal tracking.

6. **Operations & SOP Execution Engine**:
   - 10-stage end-to-end signage manufacturing & installation lifecycle:
     * *1. Sales & Requirement* &rarr; *2. Site Survey / Recce* &rarr; *3. Design & Job Card* &rarr; *4. Fabrication* &rarr; *5. Production* &rarr; *6. Packing & Dispatch* &rarr; *7. Installation* &rarr; *8. Handover & Warranty Audit*.
   - Interactive checklist verification with `Yes / No / N/A` options, remarks, and supervisor audit timestamps.

7. **Session Management & Security**:
   - Database-driven sessions tracking device, browser, OS, IP address, and last activity.
   - **Log out from other browser sessions** functionality with password confirmation.
   - Immutable security audit logs recording authentication and sensitive system operations.

8. **Reports & Downloads Vault**:
   - Real-time SLA compliance reporting, resolution rates, and streaming CSV export.
   - Downloadable SOP guidelines (`.docx`) and CRM proposals (`.pdf`).

---

## Quick Start & Setup

### Prerequisites
- PHP 8.2 or 8.3
- Composer 2.x
- MySQL / MariaDB
- Node.js & NPM

### Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/mnkkal/Signagescare.git
   cd Signagescare
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Configure Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Set your MySQL credentials in `.env` (`DB_DATABASE=signagecare_crm`, `DB_USERNAME=root`, `DB_PASSWORD=`).

4. **Run Database Migrations & Seed**:
   ```bash
   php artisan migrate --seed
   ```

5. **Install and build frontend assets**:
   ```bash
   npm install
   npm run build
   ```

6. **Link storage directory**:
   ```bash
   php artisan storage:link
   ```

7. **Start local development server**:
   ```bash
   php artisan serve
   ```
   Visit `http://localhost:8000` in your browser.

---

## Demo Accounts

The login page at `/login` provides quick-access credentials for each role:

| Role | Email | Password | Scope |
| :--- | :--- | :--- | :--- |
| **Store Manager** | `store.nagpur@lenskart.com` | `password` | Lenskart Nagpur branches, tickets & assets |
| **Super Admin** | `admin@signagecare.in` | `password` | Full system control, users & audit logs |
| **Operations Manager** | `ops@signagecare.in` | `password` | SOP execution, dispatch & SLAs |
| **Field Technician** | `rohit.tech@signagecare.in` | `password` | Assigned tickets & photo evidence |

---

## Testing

Run the automated PHPUnit test suite:
```bash
php artisan test
```

---

## License
Proprietary & Confidential &middot; Backdrop Advertising / Signage Care.
