# Apriori Data Mining — Association Rule Analysis

## Overview
A web-based data mining application that uses the Apriori algorithm to discover frequent itemsets and generate association rules from transaction data. Built with CodeIgniter 4 and PHP.

## Project Purpose
This system provides market basket analysis to help businesses identify product combinations that are frequently purchased together. The analysis outcomes can inform product placement, promotional bundling, and inventory management.

## Key Features
- **Authentication**: Secure login system with hashed passwords.
- **Transaction Management**: 
  - Import transaction data via CSV/XLSX.
  - Manual transaction entry.
  - Automatic transaction data formatting and preprocessing.
- **Apriori Analysis**: Customizable minimum support and minimum confidence thresholds.
- **Frequent Itemset Generation**: Computes k-itemsets iteratively based on transaction history.
- **Association Rule Generation**: Calculates support, confidence, and lift for each generated rule.
- **Result Management**: History of past analyses with detailed metrics.
- **Reporting**: Export results to Excel (via PhpSpreadsheet) or PDF (via Dompdf).

## Technology Stack
- PHP 8+
- CodeIgniter 4
- MySQL
- HTML/CSS/JavaScript
- PhpSpreadsheet & Dompdf (for report generation)

## System Flow
Transaction Data → Data Preparation → Apriori Processing → Frequent Itemsets → Association Rules → Analysis Results → Report Export

## Apriori Method
The system implements the standard Apriori algorithm natively in PHP:
1. **Transaction Representation**: Groups items per transaction ID.
2. **Candidate Generation (Ck)**: Generates combinations of items from previous frequent itemsets (L(k-1)).
3. **Support Calculation & Filtering (Lk)**: Counts occurrence frequencies across all transactions and filters against minimum support.
4. **Association Rule Generation**: Forms rules (Antecedent → Consequent) from frequent k-itemsets (k ≥ 2).
5. **Confidence & Lift**: Validates rules against minimum confidence and computes the lift ratio to indicate the strength of association.

## Project Structure
```text
app/
├── Controllers/
│   ├── Analisa.php
│   ├── Auth.php
│   ├── Hasil.php
│   └── Transaksi.php
├── Libraries/
│   └── apriori.php
├── Models/
│   ├── AssociationRulesModel.php
│   ├── DetailTransaksiModel.php
│   ├── FrequentItemsetModel.php
│   ├── HasilAprioriModel.php
│   ├── TransaksiModel.php
│   └── UserModel.php
├── Views/
└── Config/

public/
tests/
composer.json
```

## Installation
1. Clone the repository to your local machine.
2. Run `composer install` to install dependencies (e.g., PhpSpreadsheet, Dompdf).
3. Copy `.env.example` to `.env` and adjust your environment configurations, such as the `app.baseURL` and database credentials.
4. Create a MySQL database (e.g., `asiafarma_apriori`) and import your database schema. (Note: Initial migrations are not provided in this repository).
5. Create the required tables based on the models:
   - `users`
   - `transaksi`
   - `detail_transaksi`
   - `hasil_apriori`
   - `frequent_itemset`
   - `association_rules`
6. You can seed the default user by running the provided `UserSeeder` in `app/Database/Seeds/UserSeeder.php`.
7. Configure your web server to point to the `public/` directory, or run `php spark serve`.

## Configuration
Database connections are configured using environment variables inside the `.env` file for secure credentials management. Keep your `.env` private and never commit it to source control.

## Security Notes
- `.env` files are ignored in the repository to prevent accidental credential leakage.
- User passwords are securely hashed using bcrypt.

## Development Notes
- The core data mining logic is located in `app/Libraries/apriori.php`. This file autonomously handles the recursive Apriori candidate generation and metric calculations.
- Because Apriori candidate generation can grow exponentially in memory for large item counts, result views include hard limits on the number of generated rules fetched from the database to prevent rendering crashes.

## Limitations
- Memory constraints: Very low minimum support values on highly diverse transaction sets can lead to Out of Memory (OOM) errors due to the combinatorial explosion intrinsic to the Apriori algorithm.
