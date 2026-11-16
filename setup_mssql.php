<?php
try {
    $conn = new PDO('sqlsrv:Server=DS_A7B485CA\\MSSQLSERVER03;Database=master;TrustServerCertificate=true');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to MSSQL master successfully.\n";

    // Create database if not exists
    $conn->exec("IF DB_ID(N'SalesDB') IS NULL BEGIN CREATE DATABASE SalesDB; END");
    echo "Database SalesDB verified/created.\n";

    // Connect to SalesDB
    $db = new PDO('sqlsrv:Server=DS_A7B485CA\\MSSQLSERVER03;Database=SalesDB;TrustServerCertificate=true');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to SalesDB successfully.\n";

    // Users table
    $db->exec("
        IF OBJECT_ID(N'dbo.Users', N'U') IS NULL
        BEGIN
            CREATE TABLE dbo.Users
            (
                user_id INT IDENTITY(1,1) PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                role VARCHAR(20) DEFAULT 'user',
                created_at DATETIME2 DEFAULT (SYSDATETIME()),
                updated_at DATETIME2 DEFAULT (SYSDATETIME())
            );
        END
    ");
    echo "Table dbo.Users checked/created.\n";

    // If role column doesn't exist, add it
    $db->exec("
        IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID(N'dbo.Users') AND name = 'role')
        BEGIN
            ALTER TABLE dbo.Users ADD role VARCHAR(20) DEFAULT 'user';
        END
    ");

    // Customers table
    $db->exec("
        IF OBJECT_ID(N'dbo.Customers', N'U') IS NULL
        BEGIN
            CREATE TABLE dbo.Customers
            (
                customer_id INT IDENTITY(1,1) PRIMARY KEY,
                customer_code VARCHAR(20) UNIQUE NOT NULL,
                customer_name NVARCHAR(100) NOT NULL,
                created_at DATETIME2 DEFAULT (SYSDATETIME()),
                updated_at DATETIME2 DEFAULT (SYSDATETIME())
            );
        END
    ");
    echo "Table dbo.Customers checked/created.\n";

    // Items table
    $db->exec("
        IF OBJECT_ID(N'dbo.Items', N'U') IS NULL
        BEGIN
            CREATE TABLE dbo.Items
            (
                item_id INT IDENTITY(1,1) PRIMARY KEY,
                item_code VARCHAR(20) UNIQUE NOT NULL,
                item_name NVARCHAR(100) NOT NULL,
                description NVARCHAR(MAX),
                unit NVARCHAR(20),
                unit_price DECIMAL(10,2) NOT NULL DEFAULT 0,
                created_at DATETIME2 DEFAULT (SYSDATETIME()),
                updated_at DATETIME2 DEFAULT (SYSDATETIME())
            );
        END
    ");
    echo "Table dbo.Items checked/created.\n";

    // Sales_Orders table
    $db->exec("
        IF OBJECT_ID(N'dbo.Sales_Orders', N'U') IS NULL
        BEGIN
            CREATE TABLE dbo.Sales_Orders
            (
                order_id INT IDENTITY(1,1) PRIMARY KEY,
                order_no VARCHAR(20) UNIQUE NOT NULL,

                customer_id INT NOT NULL,

                order_date DATE NOT NULL,
                delivery_date DATE,

                total_amount DECIMAL(15,2) DEFAULT 0,
                discount_percent DECIMAL(5,2) DEFAULT 0,
                discount_amount DECIMAL(15,2) DEFAULT 0,
                net_amount DECIMAL(15,2) DEFAULT 0,

                vat_percent DECIMAL(5,2) DEFAULT (7),
                vat_amount DECIMAL(15,2) DEFAULT 0,
                grand_total DECIMAL(15,2) DEFAULT 0,

                status NVARCHAR(20) DEFAULT (N'draft'),

                remark NVARCHAR(MAX),

                created_by INT,

                created_at DATETIME2 DEFAULT (SYSDATETIME()),
                updated_at DATETIME2 DEFAULT (SYSDATETIME()),

                CONSTRAINT FK_SalesOrders_Customers
                    FOREIGN KEY (customer_id)
                    REFERENCES dbo.Customers(customer_id),

                CONSTRAINT FK_SalesOrders_Users
                    FOREIGN KEY (created_by)
                    REFERENCES dbo.Users(user_id),

                CONSTRAINT CK_SalesOrders_Status
                    CHECK (status IN (N'draft', N'confirmed', N'cancelled'))
            );
        END
    ");
    echo "Table dbo.Sales_Orders checked/created.\n";

    // Sales_Lines table
    $db->exec("
        IF OBJECT_ID(N'dbo.Sales_Lines', N'U') IS NULL
        BEGIN
            CREATE TABLE dbo.Sales_Lines
            (
                line_id INT IDENTITY(1,1) PRIMARY KEY,

                order_id INT NOT NULL,
                item_id INT NOT NULL,

                quantity DECIMAL(10,3) NOT NULL DEFAULT 1,
                unit_price DECIMAL(10,2) NOT NULL DEFAULT 0,
                discount_percent DECIMAL(5,2) DEFAULT 0,
                discount_amount DECIMAL(10,2) DEFAULT 0,
                line_total DECIMAL(15,2) NOT NULL DEFAULT 0,

                remark NVARCHAR(255),

                line_no INT,

                created_at DATETIME2 DEFAULT (SYSDATETIME()),
                updated_at DATETIME2 DEFAULT (SYSDATETIME()),

                CONSTRAINT FK_SalesLines_SalesOrders
                    FOREIGN KEY (order_id)
                    REFERENCES dbo.Sales_Orders(order_id) ON DELETE CASCADE,

                CONSTRAINT FK_SalesLines_Items
                    FOREIGN KEY (item_id)
                    REFERENCES dbo.Items(item_id)
            );
        END
    ");
    echo "Table dbo.Sales_Lines checked/created.\n";

    echo "All tables created successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
