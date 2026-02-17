<?php
$pageTitle = 'Billing';
require_once '../config/db.php';
require_once '../components/header.php';

$db = new Database();
$pdo = $db->connect();

// Handle new invoice creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create_invoice') {
        try {
            $pdo->beginTransaction();
            
            // Generate invoice number
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);
            
            // Insert invoice
            $stmt = $pdo->prepare("
                INSERT INTO invoices (invoice_number, customer_id, total_amount, gst_amount, discount, created_by) 
                VALUES (?, ?, ?, ?, ?, ?) RETURNING id
            ");
            $stmt->execute([
                $invoiceNumber,
                $_POST['customer_id'] ?: null,
                $_POST['total_amount'],
                $_POST['gst_amount'] ?? 0,
                $_POST['discount'] ?? 0,
                $_SESSION['user_id']
            ]);
            
            $invoiceId = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
            
            // Insert invoice items
            $products = json_decode($_POST['products'], true);
            foreach ($products as $item) {
                $stmt = $pdo->prepare("
                    INSERT INTO invoice_items (invoice_id, product_id, quantity, price, total) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $invoiceId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['price'],
                    $item['quantity'] * $item['price']
                ]);
                
                // Update stock
                $stmt = $pdo->prepare("
                    UPDATE products SET stock = stock - ? WHERE id = ?
                ");
                $stmt->execute([$item['quantity'], $item['product_id']]);
                
                // Record stock history
                $stmt = $pdo->prepare("
                    INSERT INTO stock_history (product_id, quantity_change, type, reference_id) 
                    VALUES (?, ?, 'out', ?)
                ");
                $stmt->execute([$item['product_id'], -$item['quantity'], $invoiceId]);
            }
            
            $pdo->commit();
            $success = "Invoice created successfully! Invoice #: " . $invoiceNumber;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error creating invoice: " . $e->getMessage();
        }
    }
}

// Get customers for dropdown
$customers = $pdo->query("SELECT id, name, phone FROM customers ORDER BY name")->fetchAll();

// Get products for dropdown
$products = $pdo->query("SELECT id, name, price, stock FROM products WHERE stock > 0 ORDER BY name")->fetchAll();

// Get recent invoices
$invoices = $pdo->query("
    SELECT i.*, c.name as customer_name 
    FROM invoices i 
    LEFT JOIN customers c ON i.customer_id = c.id 
    ORDER BY i.created_at DESC LIMIT 10
")->fetchAll();
?>

<div class="bg-white rounded-lg shadow-lg p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-[#4d2c0b]">Create New Invoice</h2>
        <button onclick="toggleInvoiceForm()" class="bg-[#713600] text-white px-4 py-2 rounded hover:bg-[#4d2c0b]">
            <i class="fas fa-plus"></i> New Invoice
        </button>
    </div>
    
    <!-- Invoice Form (Hidden by default) -->
    <div id="invoiceForm" class="hidden border-t pt-4">
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" id="billingForm">
            <input type="hidden" name="action" value="create_invoice">
            <input type="hidden" name="products" id="productsData">
            <input type="hidden" name="total_amount" id="totalAmount">
            <input type="hidden" name="gst_amount" id="gstAmount">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-[#4d2c0b] text-sm font-bold mb-2">Customer</label>
                    <select name="customer_id" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-[#713600]">
                        <option value="">Walk-in Customer</option>
                        <?php foreach ($customers as $customer): ?>
                        <option value="<?php echo $customer['id']; ?>">
                            <?php echo $customer['name'] . ' - ' . $customer['phone']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-[#4d2c0b] text-sm font-bold mb-2">Discount (₹)</label>
                    <input type="number" name="discount" id="discount" value="0" min="0" step="0.01"
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-[#713600]">
                </div>
            </div>
            
            <!-- Product Selection -->
            <div class="mb-4">
                <h3 class="text-md font-semibold text-[#4d2c0b] mb-2">Add Products</h3>
                <div class="flex gap-2 mb-2">
                    <select id="productSelect" class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:border-[#713600]">
                        <option value="">Select Product</option>
                        <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['id']; ?>" 
                                data-name="<?php echo $product['name']; ?>"
                                data-price="<?php echo $product['price']; ?>"
                                data-stock="<?php echo $product['stock']; ?>">
                            <?php echo $product['name'] . ' - ₹' . $product['price'] . ' (Stock: ' . $product['stock'] . ')'; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" id="quantity" placeholder="Qty" min="1" value="1" 
                           class="w-24 px-3 py-2 border rounded-lg focus:outline-none focus:border-[#713600]">
                    <button type="button" onclick="addProduct()" 
                            class="bg-[#d16a02] text-white px-4 py-2 rounded hover:bg-[#4d2c0b]">
                        Add
                    </button>
                </div>
                
                <!-- Selected Products Table -->
                <table class="w-full mt-4" id="selectedProducts">
                    <thead>
                        <tr class="text-left text-[#4d2c0b] border-b">
                            <th class="pb-2">Product</th>
                            <th class="pb-2">Price</th>
                            <th class="pb-2">Quantity</th>
                            <th class="pb-2">Total</th>
                            <th class="pb-2">Action</th>
                        </tr>
                    </thead>
                    <tbody id="cartItems">
                        <!-- Cart items will be added here dynamically -->
                    </tbody>
                    <tfoot>
                        <tr class="border-t">
                            <td colspan="3" class="text-right font-bold pt-2">Subtotal:</td>
                            <td class="pt-2 font-bold" id="subtotal">₹0.00</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right">GST (18%):</td>
                            <td id="gst">₹0.00</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right">Discount:</td>
                            <td id="discountDisplay">₹0.00</td>
                            <td></td>
                        </tr>
                        <tr class="text-lg">
                            <td colspan="3" class="text-right font-bold">Total:</td>
                            <td class="font-bold text-[#713600]" id="grandTotal">₹0.00</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" onclick="generateInvoice()" 
                        class="bg-[#713600] text-white px-6 py-2 rounded hover:bg-[#4d2c0b]">
                    Generate Invoice
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Recent Invoices -->
<div class="bg-white rounded-lg shadow-lg p-6">
    <h2 class="text-lg font-semibold text-[#4d2c0b] mb-4">Recent Invoices</h2>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-[#4d2c0b] border-b">
                    <th class="pb-2">Invoice #</th>
                    <th class="pb-2">Customer</th>
                    <th class="pb-2">Amount</th>
                    <th class="pb-2">Date</th>
                    <th class="pb-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoices as $invoice): ?>
                <tr class="border-b">
                    <td class="py-2"><?php echo $invoice['invoice_number']; ?></td>
                    <td class="py-2"><?php echo $invoice['customer_name'] ?? 'Walk-in'; ?></td>
                    <td class="py-2 text-[#713600] font-semibold">₹<?php echo number_format($invoice['total_amount'], 2); ?></td>
                    <td class="py-2"><?php echo date('d M Y', strtotime($invoice['created_at'])); ?></td>
                    <td class="py-2">
                        <button onclick="printInvoice(<?php echo $invoice['id']; ?>)" 
                                class="text-[#713600] hover:text-[#4d2c0b] mr-2">
                            <i class="fas fa-print"></i>
                        </button>
                        <button onclick="shareWhatsApp(<?php echo $invoice['id']; ?>)" 
                                class="text-green-600 hover:text-green-800">
                            <i class="fab fa-whatsapp"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
let cart = [];

function toggleInvoiceForm() {
    document.getElementById('invoiceForm').classList.toggle('hidden');
}

function addProduct() {
    const select = document.getElementById('productSelect');
    const quantity = document.getElementById('quantity').value;
    
    if (!select.value) {
        alert('Please select a product');
        return;
    }
    
    const option = select.options[select.selectedIndex];
    const product = {
        id: select.value,
        name: option.dataset.name,
        price: parseFloat(option.dataset.price),
        stock: parseInt(option.dataset.stock),
        quantity: parseInt(quantity)
    };
    
    if (product.quantity > product.stock) {
        alert('Insufficient stock! Available: ' + product.stock);
        return;
    }
    
    // Check if product already in cart
    const existing = cart.findIndex(p => p.id === product.id);
    if (existing >= 0) {
        if (cart[existing].quantity + product.quantity > product.stock) {
            alert('Insufficient stock! Available: ' + product.stock);
            return;
        }
        cart[existing].quantity += product.quantity;
    } else {
        cart.push(product);
    }
    
    updateCart();
}

function updateCart() {
    const tbody = document.getElementById('cartItems');
    tbody.innerHTML = '';
    let subtotal = 0;
    
    cart.forEach((item, index) => {
        const total = item.price * item.quantity;
        subtotal += total;
        
        tbody.innerHTML += `
            <tr>
                <td class="py-2">${item.name}</td>
                <td class="py-2">₹${item.price.toFixed(2)}</td>
                <td class="py-2">${item.quantity}</td>
                <td class="py-2">₹${total.toFixed(2)}</td>
                <td class="py-2">
                    <button onclick="removeFromCart(${index})" class="text-red-600">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const gst = subtotal * 0.18;
    const total = subtotal + gst - discount;
    
    document.getElementById('subtotal').innerHTML = `₹${subtotal.toFixed(2)}`;
    document.getElementById('gst').innerHTML = `₹${gst.toFixed(2)}`;
    document.getElementById('discountDisplay').innerHTML = `₹${discount.toFixed(2)}`;
    document.getElementById('grandTotal').innerHTML = `₹${total.toFixed(2)}`;
    
    document.getElementById('totalAmount').value = total;
    document.getElementById('gstAmount').value = gst;
}

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCart();
}

function generateInvoice() {
    if (cart.length === 0) {
        alert('Please add products to the invoice');
        return;
    }
    
    document.getElementById('productsData').value = JSON.stringify(cart);
    document.getElementById('billingForm').submit();
}

function printInvoice(id) {
    window.open('/modules/print_invoice.php?id=' + id, '_blank');
}

function shareWhatsApp(id) {
    window.open('https://wa.me/?text=Check%20your%20invoice%20from%20SmartBiz%20-%20Invoice%20ID:%20' + id, '_blank');
}

document.getElementById('discount').addEventListener('input', updateCart);
</script>

<?php require_once __DIR__ . '/../components/footer.php'; ?>
