Here’s the system flow in simple terms.

## 1. Big Picture

The POS has **two sides**:

**Cashier side:**
Used to sell items, print receipts, collect payments, and close shifts.

**Admin/Manager side:**
Used to manage products, stock, staff, expenses, reports, and audit logs.

The system should always answer these questions:

**What was sold?**
**Who sold it?**
**How was payment received?**
**How much stock remains?**
**Was anything changed, refunded, discounted, or adjusted?**
**Who approved it?**

---

## 2. Main System Flow

### Step 1: Staff Login

Each user logs in with their own account.

Example roles:

**Cashier** can sell and print receipts.
**Manager** can approve discounts, refunds, and cancellations.
**Inventory Officer** can update stock.
**Accountant** can view expenses and reports.
**Admin** controls everything.

Every action is recorded in the audit log.

---

### Step 2: Shift Opening

Before selling starts, cashier opens a shift.

They enter:

```text
Opening cash amount
Cashier name
Time started
Branch/register
```

This helps compare expected cash vs actual cash at the end of the day.

---

### Step 3: Product Setup

Admin or inventory officer creates products.

Each product should have:

```text
Product name
Category
SKU / Barcode
Cost price
Retail price
Wholesale price
Current stock
Low stock level
Supplier
```

Example:

```text
Product: Baby Diaper Large
Cost Price: GHS 80
Selling Price: GHS 100
Stock: 50 packs
Low Stock Alert: 10 packs
```

---

## 3. POS Sales Flow

Cashier opens the POS screen.

### Normal Sale

```text
Search / scan product
Add to cart
Enter quantity
System calculates total
Customer pays
Cashier completes sale
Receipt prints
Stock reduces automatically
Sale is saved
Audit log is recorded
```

Example:

```text
Sell 2 packs of diapers
Stock before: 50
Stock after: 48
Payment: Cash
Receipt printed
```

The system records:

```text
Receipt number
Items sold
Quantity
Price
Discount
Payment method
Cashier
Date/time
Profit
Stock movement
```

---

## 4. Payment Flow

The cashier can accept:

```text
Cash
Mobile Money
Bank transfer
Card
Credit / customer debt
```

### Cash Sale

System records money in cash drawer.

### Mobile Money Sale

System records money under mobile money balance.

### Credit Sale

Customer takes goods but pays later.

System records:

```text
Customer name
Amount owed
Due date if needed
Items taken
Cashier
```

This appears in the customer debt report.

---

## 5. Receipt Flow

After sale, the system prints a receipt.

Receipt includes:

```text
Business name
Receipt number
Date/time
Cashier
Items
Quantity
Unit price
Discount
Total
Payment method
Amount paid
Change
Thank-you message
```

Printing can be through:

```text
Browser print
Thermal printer
A4 invoice
```

---

## 6. Inventory Flow

Inventory changes happen automatically or manually.

### Automatic Stock-Out

When a sale happens:

```text
Sale completed → stock reduces
```

### Stock-In

When new goods arrive:

```text
Supplier delivers stock
Inventory officer records purchase
Stock increases
Cost is recorded
Supplier payable is updated if not fully paid
```

### Stock Adjustment

Used for damaged, missing, counted, or corrected stock.

Example:

```text
Product: Tissue Roll
System stock: 100
Physical count: 95
Adjustment: -5
Reason: Missing after stock count
Approved by: Manager
```

Every adjustment must require a reason and user record.

---

## 7. Offline Mode Flow

This is very important.

When internet is available:

```text
POS uses online server
Sales save to main database
Stock updates immediately
```

When internet goes off:

```text
POS switches to offline mode
Sales are saved locally in IndexedDB
Receipts can still print
Stock reduces locally
Sync status shows “Pending Sync”
```

When internet returns:

```text
System detects connection
Offline sales upload to server
Server confirms saved sales
Stock is updated
Local records are marked synced
Manager can review sync report
```

The cashier should see something like:

```text
Online
Offline - 5 sales pending sync
Syncing...
All sales synced
```

---

## 8. Offline Sync Conflict Flow

Conflict can happen when two devices sell the same stock while offline.

Example:

```text
Server stock: 10 packs
Device A offline sells 7
Device B offline sells 6
Total sold offline: 13
But stock was only 10
```

The system should still save the sales, but flag it:

```text
Stock conflict detected
Product oversold by 3
Manager review required
```

This helps detect mistakes or possible theft.

---

## 9. Refund / Cancellation Flow

Cashier should not freely cancel or refund sales.

Flow:

```text
Cashier selects sale
Clicks refund/cancel
System asks for reason
Manager approval required
Refund is recorded
Stock is returned if applicable
Refund receipt prints
Audit log is saved
```

Example audit log:

```text
Sale #1021 refunded
Reason: Wrong item sold
Cashier: Ama
Approved by: Manager Kojo
Time: 4:35 PM
```

---

## 10. Expense Flow

Accountant or manager records expenses.

Example:

```text
Rent
Transport
Electricity
Staff lunch
Repairs
Packaging
```

Each expense should include:

```text
Amount
Category
Description
Payment method
Date
Recorded by
Receipt attachment if needed
```

Expenses reduce net profit.

---

## 11. Profit Flow

For each sale, profit is calculated like this:

```text
Selling price - cost price = gross profit
Gross profit - expenses = net profit
```

Example:

```text
Diaper cost price: GHS 80
Selling price: GHS 100
Profit: GHS 20
```

Reports should show:

```text
Sales income
Cost of goods sold
Gross profit
Expenses
Net profit
```

---

## 12. Daily Closing Flow

At the end of the day, cashier closes shift.

System shows:

```text
Expected cash
Expected mobile money
Expected card/bank transfer
Refunds
Discounts
Credit sales
Expenses paid from drawer
```

Cashier enters actual cash counted.

System compares:

```text
Expected cash: GHS 2,500
Actual cash: GHS 2,450
Shortage: GHS 50
```

Manager reviews shortages or excess cash.

---

## 13. Theft Detection Flow

The system detects suspicious activity by checking:

```text
Too many refunds
Too many discounts
Frequent sale cancellations
Stock adjustments without good reason
Cash shortage
Stock count mismatch
Offline sales not synced
Selling below allowed price
Deleted or edited records
```

Example alert:

```text
Cashier Kofi gave 12 discounts today.
Manager review recommended.
```

---

## 14. Main Data Flow Summary

```text
Login
→ Open shift
→ Sell products
→ Print receipt
→ Reduce stock
→ Record payment
→ Record profit
→ Save audit log
→ Sync if offline
→ Close shift
→ Generate reports
```

---

## 15. Simple Technical Flow

```text
Frontend POS
↓
IndexedDB local storage
↓
Sync engine
↓
Laravel/PHP REST API
↓
MySQL database
↓
Reports/dashboard
```

Offline mode:

```text
POS screen
↓
IndexedDB
↓
Pending sync queue
↓
Internet returns
↓
API sync
↓
MySQL updated
```

---

## 16. Recommended Build Order

Build it in this order:

```text
1. Login and roles
2. Product and category setup
3. Basic POS sales
4. Receipt printing
5. Inventory stock-in and stock-out
6. Sales history
7. Shift opening and closing
8. Expenses
9. Reports
10. Offline mode
11. Sync engine
12. Audit logs and theft detection
```

The most important rule: **no sale, refund, stock change, discount, or cash movement should happen without being recorded with the user, time, and reason.**