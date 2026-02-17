<?php
$pageTitle = 'Customer Management';
require_once '../config/db.php';
require_once '../components/header.php';

$db = new Database();
$pdo = $db->connect();

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $stmt = $pdo->prepare("INSERT INTO customers (name, phone, email, address, notes) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$_POST['name'], $_POST['phone'], $_POST['email'], $_POST['address'], $_POST['notes']]);
                $success = "Customer added successfully!";
                break;
                
            case 'edit':
                $stmt = $pdo->prepare("UPDATE customers SET name=?, phone=?, email=?, address=?, notes=? WHERE id=?");
                $stmt->execute([$_POST['name'], $_POST['phone'], $_POST['email'], $_POST['address'], $_POST['notes'], $_POST['id']]);
                $success = "Customer updated successfully!";
                break;
                
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM customers WHERE id=?");
                $stmt->execute([$_POST['id']]);
                $success = "Customer deleted successfully!";
                break;
                
            case 'export':
                exportCustomers($pdo);
                break;
        }
    }
}

function exportCustomers($pdo) {
    $stmt = $pdo->query("SELECT * FROM customers ORDER BY name");
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="customers_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Name', 'Phone', 'Email', 'Address', 'Notes', 'Created At']);
    
    foreach ($customers as $customer) {
        fputcsv($output, $customer);
    }
    fclose($output);
    exit();
}

// Get all customers
$customers = $pdo->query("SELECT * FROM customers ORDER BY created_at DESC")->fetchAll();
?>

<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold text-[#4d2c0b]">Customers</h2>
        <div class="flex gap-2">
            <button onclick="openCustomerModal()" class="bg-[#713600] text-white px-4 py-2 rounded hover:bg-[#4d2c0b]">
                <i class="fas fa-plus"></i> Add Customer
            </button>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="action" value="export">
                <button type="submit" class="bg-[#d16a02] text-white px-4 py-2 rounded hover:bg-[#4d2c0b]">
                    <i class="fas fa-download"></i> Export CSV
                </button>
            </form>
        </div>
    </div>
    
    <?php if (isset($success)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>
    
    <!-- Search Bar -->
    <div class="mb-4">
        <input type="text" id="searchCustomer" placeholder="Search customers..." 
               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-[#713600]">
    </div>
    
    <!-- Customers Table -->
    <div class="overflow-x-auto">
        <table class="w-full" id="customersTable">
            <thead>
                <tr class="text-left text-[#4d2c0b] border-b">
                    <th class="pb-2">Name</th>
                    <th class="pb-2">Phone</th>
                    <th class="pb-2">Email</th>
                    <th class="pb-2">Address</th>
                    <th class="pb-2">Total Purchases</th>
                    <th class="pb-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): 
                    // Get total purchases for this customer
                    $stmt = $pdo->prepare("SELECT COUNT(*), COALESCE(SUM(total_amount), 0) as total FROM invoices WHERE customer_id = ?");
                    $stmt->execute([$customer['id']]);
                    $purchases = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>
                <tr class="border-b">
                    <td class="py-2 font-medium"><?php echo htmlspecialchars($customer['name']); ?></td>
                    <td class="py-2"><?php echo htmlspecialchars($customer['phone']); ?></td>
                    <td class="py-2"><?php echo htmlspecialchars($customer['email']); ?></td>
                    <td class="py-2"><?php echo htmlspecialchars(substr($customer['address'], 0, 30)) . '...'; ?></td>
                    <td class="py-2 text-[#713600] font-semibold">
                        <?php echo $purchases['count']; ?> invoices (₹<?php echo number_format($purchases['total'], 2); ?>)
                    </td>
                    <td class="py-2">
                        <button onclick="viewCustomer(<?php echo $customer['id']; ?>)" 
                                class="text-blue-600 hover:text-blue-800 mr-2" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick="editCustomer(<?php echo htmlspecialchars(json_encode($customer)); ?>)" 
                                class="text-[#713600] hover:text-[#4d2c0b] mr-2" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteCustomer(<?php echo $customer['id']; ?>)" 
                                class="text-red-600 hover:text-red-800" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Customer Modal -->
<div id="customerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold text-[#4d2c0b] mb-4" id="modalTitle">Add Customer</h3>
        
        <form method="POST" id="customerForm">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="customerId">
            
            <div class="mb-3">
                <label class="block text-[#4d2c0b] text-sm font-bold mb-2">Name *</label>
                <input type="text" name="name" id="customerName" required
                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-[#713600]">
            </div>
            
            <div class="mb-3">
                <label class="block text-[#4d2c0b] text-sm font-bold mb-2">Phone</label>
                <input type="tel" name="phone" id="customerPhone"
                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-[#713600]">
            </div>
            
            <div class="mb-3">
                <label class="block text-[#4d2c0b] text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" id="customerEmail"
                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-[#713600]">
            </div>
            
            <div class="mb-3">
                <label class="block text-[#4d2c0b] text-sm font-bold mb-2">Address</label>
                <textarea name="address" id="customerAddress" rows="2"
                          class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-[#713600]"></textarea>
            </div>
            
            <div class="mb-4">
                <label class="block text-[#4d2c0b] text-sm font-bold mb-2">Notes</label>
                <textarea name="notes" id="customerNotes" rows="2"
                          class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-[#713600]"></textarea>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeCustomerModal()" 
                        class="px-4 py-2 border rounded-lg hover:bg-gray-100">
                    Cancel
                </button>
                <button type="submit" 
                        class="bg-[#713600] text-white px-4 py-2 rounded-lg hover:bg-[#4d2c0b]">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Customer Details Modal -->
<div id="viewCustomerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl">
        <h3 class="text-lg font-semibold text-[#4d2c0b] mb-4">Customer Details</h3>
        
        <div id="customerDetails" class="mb-4">
            <!-- Details will be loaded here -->
        </div>
        
        <div class="flex justify-end">
            <button type="button" onclick="closeViewModal()" 
                    class="bg-[#713600] text-white px-4 py-2 rounded-lg hover:bg-[#4d2c0b]">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function openCustomerModal() {
    document.getElementById('modalTitle').textContent = 'Add Customer';
    document.getElementById('formAction').value = 'add';
    document.getElementById('customerId').value = '';
    document.getElementById('customerName').value = '';
    document.getElementById('customerPhone').value = '';
    document.getElementById('customerEmail').value = '';
    document.getElementById('customerAddress').value = '';
    document.getElementById('customerNotes').value = '';
    document.getElementById('customerModal').style.display = 'flex';
}

function editCustomer(customer) {
    document.getElementById('modalTitle').textContent = 'Edit Customer';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('customerId').value = customer.id;
    document.getElementById('customerName').value = customer.name;
    document.getElementById('customerPhone').value = customer.phone || '';
    document.getElementById('customerEmail').value = customer.email || '';
    document.getElementById('customerAddress').value = customer.address || '';
    document.getElementById('customerNotes').value = customer.notes || '';
    document.getElementById('customerModal').style.display = 'flex';
}

function closeCustomerModal() {
    document.getElementById('customerModal').style.display = 'none';
}

function deleteCustomer(id) {
    if (confirm('Are you sure you want to delete this customer?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function viewCustomer(id) {
    fetch(`get_customer.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            let html = `
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Name</p>
                        <p class="font-medium">${data.name}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Phone</p>
                        <p class="font-medium">${data.phone || '-'}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-medium">${data.email || '-'}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Address</p>
                        <p class="font-medium">${data.address || '-'}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-sm text-gray-600">Notes</p>
                        <p class="font-medium">${data.notes || '-'}</p>
                    </div>
                </div>
                
                <h4 class="font-semibold text-[#4d2c0b] mt-4 mb-2">Purchase History</h4>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="pb-2">Invoice #</th>
                                <th class="pb-2">Date</th>
                                <th class="pb-2">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            data.invoices.forEach(inv => {
                html += `
                    <tr class="border-b">
                        <td class="py-1">${inv.invoice_number}</td>
                        <td class="py-1">${inv.created_at}</td>
                        <td class="py-1 text-[#713600]">₹${parseFloat(inv.total_amount).toFixed(2)}</td>
                    </tr>
                `;
            });
            
            html += `
                        </tbody>
                    </table>
                </div>
            `;
            
            document.getElementById('customerDetails').innerHTML = html;
            document.getElementById('viewCustomerModal').style.display = 'flex';
        });
}

function closeViewModal() {
    document.getElementById('viewCustomerModal').style.display = 'none';
}

// Search functionality
document.getElementById('searchCustomer').addEventListener('keyup', function() {
    const searchText = this.value.toLowerCase();
    const table = document.getElementById('customersTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const name = row.cells[0].textContent.toLowerCase();
        const phone = row.cells[1].textContent.toLowerCase();
        const email = row.cells[2].textContent.toLowerCase();
        
        if (name.includes(searchText) || phone.includes(searchText) || email.includes(searchText)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
});

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('customerModal');
    const viewModal = document.getElementById('viewCustomerModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
    if (event.target === viewModal) {
        viewModal.style.display = 'none';
    }
}
</script>

<?php require_once __DIR__ . '/../components/footer.php'; ?>
