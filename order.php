<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

echo "<h2>Welcome, {$_SESSION['username']}!</h2>";
echo "<a href='logout.php'>Logout</a><br><br>";


$total_cost = 0;
$change = 0;
$message = "";
$ordered_item_name = "";
$ordered_qty = 0;

if (isset($_POST['order'])) {
    $item_id = $_POST['item_id'];
    $ordered_qty = $_POST['quantity'];
    $money = $_POST['money'];

  
    $query = mysqli_query($conn, "SELECT * FROM inventory WHERE id=$item_id");
    $row = mysqli_fetch_assoc($query);

    $ordered_item_name = $row['item_name'];
    $price = $row['price'];
    $stock = $row['quantity'];

    if ($ordered_qty > $stock) {
        $message = "<span style='color:red;'>Not enough stock available!</span>";
    } else {
        $total_cost = $ordered_qty * $price;

        if ($money >= $total_cost) {
            $change = $money - $total_cost;

           
            $new_stock = $stock - $ordered_qty;
            mysqli_query($conn, 
                "UPDATE inventory SET quantity = $new_stock WHERE id = $item_id"
            );

            $message = "<span style='color:green;'>Order successful!</span>";
        } else {
            $message = "<span style='color:red;'>Not enough money.</span>";
        }
    }
}
?>

<h3>Order Items</h3>
<form method="POST">
    <label>Select Item:</label>
    <select name="item_id" required>
        <?php
        $items = mysqli_query($conn, "SELECT * FROM inventory");
        while ($item = mysqli_fetch_assoc($items)) {
            echo "<option value='{$item['id']}'>
                    {$item['item_name']} - ₱{$item['price']} (Stock: {$item['quantity']})
                  </option>";
        }
        ?>
    </select><br><br>

    Quantity: <input type="number" name="quantity" min="1" required><br><br>
    Your payment: <input type="number" name="money" step="0.01" min="0" required><br><br>

    <button type="submit" name="order">Place Order</button>
</form>

<?php
if (isset($_POST['order'])) {
    echo "<hr>";
    echo "<h3>Order Summary</h3>";
    echo "Item: <strong>$ordered_item_name</strong><br>";
    echo "Quantity: <strong>$ordered_qty</strong><br>";
    echo "Total Cost: ₱" . number_format($total_cost, 2) . "<br>";

    if ($money >= $total_cost) {
        echo "Change: ₱" . number_format($change, 2) . "<br>";
    }

    echo "<br>$message";
}
?>
