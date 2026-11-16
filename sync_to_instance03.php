<?php
try {
    echo "Connecting to target (DS_A7B485CA\\MSSQLSERVER03)...\n";
    $targetDB = new PDO("sqlsrv:Server=DS_A7B485CA\\MSSQLSERVER03;Database=SalesDB;TrustServerCertificate=true;LoginTimeout=5", '', '');
    $targetDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ensure columns on Users
    $targetDB->exec("
        IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID(N'dbo.Users') AND name = 'role')
        BEGIN
            ALTER TABLE dbo.Users ADD role VARCHAR(20) DEFAULT 'user';
        END
    ");
    $targetDB->exec("
        IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID(N'dbo.Users') AND name = 'created_at')
        BEGIN
            ALTER TABLE dbo.Users ADD created_at DATETIME2 DEFAULT (SYSDATETIME());
        END
    ");
    $targetDB->exec("
        IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID(N'dbo.Users') AND name = 'updated_at')
        BEGIN
            ALTER TABLE dbo.Users ADD updated_at DATETIME2 DEFAULT (SYSDATETIME());
        END
    ");
    $targetDB->exec("ALTER TABLE dbo.Users ALTER COLUMN password VARCHAR(255);");
    echo "Users columns verified on MSSQLSERVER03.\n";

    // Ensure columns on Customers
    $targetDB->exec("
        IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID(N'dbo.Customers') AND name = 'created_at')
        BEGIN
            ALTER TABLE dbo.Customers ADD created_at DATETIME2 DEFAULT (SYSDATETIME());
        END
    ");
    $targetDB->exec("
        IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID(N'dbo.Customers') AND name = 'updated_at')
        BEGIN
            ALTER TABLE dbo.Customers ADD updated_at DATETIME2 DEFAULT (SYSDATETIME());
        END
    ");

    // Ensure columns on Items
    $targetDB->exec("
        IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID(N'dbo.Items') AND name = 'category')
        BEGIN
            ALTER TABLE dbo.Items ADD category NVARCHAR(50) DEFAULT 'IT & Hardware';
        END
    ");
    $targetDB->exec("
        IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID(N'dbo.Items') AND name = 'created_at')
        BEGIN
            ALTER TABLE dbo.Items ADD created_at DATETIME2 DEFAULT (SYSDATETIME());
        END
    ");
    $targetDB->exec("
        IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID(N'dbo.Items') AND name = 'updated_at')
        BEGIN
            ALTER TABLE dbo.Items ADD updated_at DATETIME2 DEFAULT (SYSDATETIME());
        END
    ");
    echo "Customers & Items columns verified on MSSQLSERVER03.\n";

    // Connecting to source
    $source = new PDO("sqlsrv:Server=localhost;Database=SalesDB;TrustServerCertificate=true;LoginTimeout=3", '', '');
    $source->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Copy Users
    $sourceUsers = $source->query("SELECT * FROM dbo.Users")->fetchAll(PDO::FETCH_ASSOC);
    $targetDB->exec("SET IDENTITY_INSERT dbo.Users ON");
    foreach ($sourceUsers as $u) {
        $check = $targetDB->prepare("SELECT COUNT(*) FROM dbo.Users WHERE user_id = ?");
        $check->execute([$u['user_id']]);
        if ($check->fetchColumn() == 0) {
            $stmt = $targetDB->prepare("INSERT INTO dbo.Users (user_id, username, password, role, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$u['user_id'], $u['username'], $u['password'], $u['role'], $u['created_at'], $u['updated_at']]);
        }
    }
    $targetDB->exec("SET IDENTITY_INSERT dbo.Users OFF");
    echo "Copied " . count($sourceUsers) . " users to target.\n";

    // Copy Customers
    $sourceCustomers = $source->query("SELECT * FROM dbo.Customers")->fetchAll(PDO::FETCH_ASSOC);
    $targetDB->exec("SET IDENTITY_INSERT dbo.Customers ON");
    foreach ($sourceCustomers as $c) {
        $check = $targetDB->prepare("SELECT COUNT(*) FROM dbo.Customers WHERE customer_id = ?");
        $check->execute([$c['customer_id']]);
        if ($check->fetchColumn() == 0) {
            $stmt = $targetDB->prepare("INSERT INTO dbo.Customers (customer_id, customer_code, customer_name, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$c['customer_id'], $c['customer_code'], $c['customer_name'], $c['created_at'], $c['updated_at']]);
        }
    }
    $targetDB->exec("SET IDENTITY_INSERT dbo.Customers OFF");
    echo "Copied " . count($sourceCustomers) . " customers to target.\n";

    // Copy Items
    $sourceItems = $source->query("SELECT * FROM dbo.Items")->fetchAll(PDO::FETCH_ASSOC);
    $targetDB->exec("SET IDENTITY_INSERT dbo.Items ON");
    foreach ($sourceItems as $it) {
        $check = $targetDB->prepare("SELECT COUNT(*) FROM dbo.Items WHERE item_id = ?");
        $check->execute([$it['item_id']]);
        if ($check->fetchColumn() == 0) {
            $stmt = $targetDB->prepare("INSERT INTO dbo.Items (item_id, item_code, item_name, category, description, unit, unit_price, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$it['item_id'], $it['item_code'], $it['item_name'], $it['category'] ?? 'IT & Hardware', $it['description'], $it['unit'], $it['unit_price'], $it['created_at'], $it['updated_at']]);
        }
    }
    $targetDB->exec("SET IDENTITY_INSERT dbo.Items OFF");
    echo "Copied " . count($sourceItems) . " items to target.\n";

    // Copy Sales_Orders
    $sourceOrders = $source->query("SELECT * FROM dbo.Sales_Orders")->fetchAll(PDO::FETCH_ASSOC);
    $targetDB->exec("SET IDENTITY_INSERT dbo.Sales_Orders ON");
    foreach ($sourceOrders as $ord) {
        $check = $targetDB->prepare("SELECT COUNT(*) FROM dbo.Sales_Orders WHERE order_id = ?");
        $check->execute([$ord['order_id']]);
        if ($check->fetchColumn() == 0) {
            $stmt = $targetDB->prepare("INSERT INTO dbo.Sales_Orders (order_id, order_no, customer_id, order_date, delivery_date, total_amount, discount_percent, discount_amount, net_amount, vat_percent, vat_amount, grand_total, status, remark, created_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $ord['order_id'], $ord['order_no'], $ord['customer_id'], $ord['order_date'], $ord['delivery_date'],
                $ord['total_amount'], $ord['discount_percent'], $ord['discount_amount'], $ord['net_amount'],
                $ord['vat_percent'], $ord['vat_amount'], $ord['grand_total'], $ord['status'], $ord['remark'],
                $ord['created_by'], $ord['created_at'], $ord['updated_at']
            ]);
        }
    }
    $targetDB->exec("SET IDENTITY_INSERT dbo.Sales_Orders OFF");
    echo "Copied " . count($sourceOrders) . " orders to target.\n";

    // Copy Sales_Lines
    $sourceLines = $source->query("SELECT * FROM dbo.Sales_Lines")->fetchAll(PDO::FETCH_ASSOC);
    $targetDB->exec("SET IDENTITY_INSERT dbo.Sales_Lines ON");
    foreach ($sourceLines as $ln) {
        $check = $targetDB->prepare("SELECT COUNT(*) FROM dbo.Sales_Lines WHERE line_id = ?");
        $check->execute([$ln['line_id']]);
        if ($check->fetchColumn() == 0) {
            $stmt = $targetDB->prepare("INSERT INTO dbo.Sales_Lines (line_id, order_id, item_id, quantity, unit_price, discount_percent, discount_amount, line_total, remark, line_no, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $ln['line_id'], $ln['order_id'], $ln['item_id'], $ln['quantity'], $ln['unit_price'],
                $ln['discount_percent'], $ln['discount_amount'], $ln['line_total'], $ln['remark'],
                $ln['line_no'], $ln['created_at'], $ln['updated_at']
            ]);
        }
    }
    $targetDB->exec("SET IDENTITY_INSERT dbo.Sales_Lines OFF");
    echo "Copied " . count($sourceLines) . " lines to target.\n";

    echo "\n=== ALL DATA SUCCESSFULLY SYNCED TO DS_A7B485CA\\MSSQLSERVER03! ===\n";

} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
