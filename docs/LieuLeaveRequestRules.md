# Lieu Leave Request Rules

This document outlines the rules and logic for requesting and validating Lieu Leave within the HRPMS system. Lieu Leave is earned when an employee works on a designated off-day (e.g., a weekend or holiday) and receives approval for that work.

## Key Concepts

- **Off-Day Work**: Work performed on a non-working day that has been approved.
- **Lieu Leave**: A compensatory day off granted in exchange for approved Off-Day Work.

## Business Logic

### 1. Earning Lieu Leave

- An employee becomes eligible for Lieu Leave only after their **Off-Day Work** has been officially **approved**.
- Each approved Off-Day Work day translates into a single Lieu Leave credit.

### 2. Validity Period

- A Lieu Leave credit is valid for **one month** from the date of the approved Off-Day Work.
- If the leave is not taken within this one-month window, the Lieu Leave credit **expires** and can no longer be used.

**Example:**

- If an employee's Off-Day Work on **2025-12-16** is approved, they must use the corresponding Lieu Leave on or before **2026-01-16**.

### 3. Managing Multiple Lieu Leave Credits

- Employees can accumulate multiple Lieu Leave credits from different approved Off-Day Work dates.
- When applying for a new Lieu Leave, the system validates it against an available (un-used and non-expired) Off-Day Work credit.

**Example:**

- An employee has two approved Off-Day Work dates: **2025-12-16** and **2025-12-20**.
- The validity for these are:
    - Leave for **2025-12-16** must be taken by **2026-01-16**.
    - Leave for **2025-12-20** must be taken by **2026-01-20**.
- If the employee takes a Lieu Leave on **2026-01-15**, it will be counted against the **2025-12-16** credit. They still have until **2026-01-20** to use the credit from their work on **2025-12-20**.

### 4. Applying for a Lieu Leave

When an employee applies for a Lieu Leave on a specific date, the system determines which approved Off-Day Work dates can be used as the basis for this leave.

- **Rule**: The Off-Day Work date must be within the **one-month period immediately preceding** the requested leave date.

**Example:**

- If an employee requests a Lieu Leave for **2026-01-17**:
- The system will show a list of available, un-used, and approved Off-Day Work dates that fall between **2025-12-17** and **2026-01-17**.
- The employee can then select one of these dates to associate with their leave request.
