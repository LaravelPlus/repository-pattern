<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;

// Setup Eloquent
$capsule = new Capsule();
$capsule->addConnection(require __DIR__ . '/database/database.php');
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Run migration if users table doesn't exist
if (!Capsule::schema()->hasTable('users')) {
    require __DIR__ . '/database/create_users_table.php';
}

// Define a simple User model for Eloquent
final class User extends Model
{
    protected $table = 'users';

    protected $fillable = ['name', 'email', 'password'];

    public $timestamps = true;

    protected $dates = ['deleted_at'];
}

// Use the UserRepository prefab
require_once __DIR__ . '/../prefabs/UserRepository.php';
use Prefabs\UserRepository;

$userRepo = new UserRepository(new User());

// Handle repository actions
if (isset($_GET['delete'])) {
    $userRepo->delete((int) $_GET['delete']);
    header('Location: index.php');
    exit;
}

// Handle form submission to add a user via repository
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    if ($name && $email && $password) {
        try {
            $userRepo->create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ]);
            $message = 'User added!';
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
        }
    } else {
        $message = 'All fields are required.';
    }
}

// Handle find user by ID
$foundUser = null;
if (isset($_GET['find_id'])) {
    $findId = (int) $_GET['find_id'];
    $foundUser = $userRepo->find($findId);
    if (!$foundUser) {
        $message = 'User not found.';
    }
}

// Fetch all users via repository
$users = $userRepo->all();

// --- Repository Methods Test Section ---
$testResults = [];
$testEmail = 'testuser_' . uniqid() . '@example.com';
$testName = 'Test User';
$testPassword = 'testpass123';

// 1. Create
$createdUser = $userRepo->create([
    'name' => $testName,
    'email' => $testEmail,
    'password' => $testPassword,
]);
$testResults['create'] = $createdUser && $createdUser->email === $testEmail ? 'PASS' : 'FAIL';

// 2. Find by ID
$foundById = $userRepo->find($createdUser->id);
$testResults['find'] = $foundById && $foundById->email === $testEmail ? 'PASS' : 'FAIL';

// 3. Find by Email
$foundByEmail = $userRepo->findBy('email', $testEmail);
$testResults['findByEmail'] = $foundByEmail && $foundByEmail->id === $createdUser->id ? 'PASS' : 'FAIL';

// 4. Update by ID
$updateResult = $userRepo->update($createdUser->id, ['name' => 'Updated Name']);
$updatedUser = $userRepo->find($createdUser->id);
$testResults['updateById'] = $updateResult && $updatedUser && $updatedUser->name === 'Updated Name' ? 'PASS' : 'FAIL';

// 5. Search (by updated name)
$searchResults = $userRepo->search('Updated Name');
$testResults['search'] = $searchResults->where('id', $createdUser->id)->count() > 0 ? 'PASS' : 'FAIL';

// 6. All
$allUsers = $userRepo->all();
$testResults['all'] = $allUsers->count() > 0 ? 'PASS' : 'FAIL';

// 7. Delete
$deleteResult = $userRepo->delete($createdUser->id);
$deletedUser = $userRepo->find($createdUser->id);
$testResults['delete'] = $deleteResult && !$deletedUser ? 'PASS' : 'FAIL';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Repository Pattern Example</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="w-full py-4 px-1 sm:px-4 lg:px-8">
        <div class="w-full max-w-6xl mx-auto bg-white/80 rounded-lg shadow p-4">
            <h1 class="text-xl font-bold mb-1 text-blue-800 tracking-tight">Repository Pattern Example: Users</h1>
            <h3 class="text-sm mb-4 text-gray-600">Testing <code>prefabs/UserRepository.php</code></h3>
            <?php if ($message) { ?>
                <div class="mb-3 px-3 py-1 rounded <?php echo str_starts_with($message, 'Error') ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'; ?> text-sm">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php } ?>
            <div class="flex flex-col lg:flex-row gap-4 mb-4">
                <form method="post" class="flex-1 bg-white p-3 rounded shadow border border-gray-100">
                    <div class="flex flex-col md:flex-row gap-2">
                        <input type="text" name="name" placeholder="Name" required class="flex-1 border rounded px-2 py-1 text-sm" />
                        <input type="email" name="email" placeholder="Email" required class="flex-1 border rounded px-2 py-1 text-sm" />
                        <input type="password" name="password" placeholder="Password" required class="flex-1 border rounded px-2 py-1 text-sm" />
                        <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-sm">Add</button>
                    </div>
                </form>
                <form method="get" class="flex-1 bg-white p-3 rounded shadow border border-gray-100 flex gap-2 items-center">
                    <input type="number" name="find_id" min="1" placeholder="Find User by ID" required class="border rounded px-2 py-1 flex-1 text-sm" />
                    <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-sm">Find</button>
                </form>
            </div>
            <?php if ($foundUser) { ?>
                <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-3 rounded">
                    <div class="font-semibold mb-1 text-base">User found:</div>
                    <div class="text-sm"><span class="font-medium">ID:</span> <?= $foundUser->id ?></div>
                    <div class="text-sm"><span class="font-medium">Name:</span> <?= htmlspecialchars($foundUser->name) ?></div>
                    <div class="text-sm"><span class="font-medium">Email:</span> <?= htmlspecialchars($foundUser->email) ?></div>
                    <div class="text-sm"><span class="font-medium">Created At:</span> <?= $foundUser->created_at ?></div>
                </div>
            <?php } ?>
            <h2 class="text-lg font-semibold mb-2 mt-6">All Users</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded shadow border border-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-3 text-left">ID</th>
                            <th class="py-2 px-3 text-left">Name</th>
                            <th class="py-2 px-3 text-left">Email</th>
                            <th class="py-2 px-3 text-left">Created At</th>
                            <th class="py-2 px-3 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user) { ?>
                            <tr class="border-t hover:bg-blue-50 transition">
                                <td class="py-2 px-3 font-mono text-blue-900"><?= $user->id ?></td>
                                <td class="py-2 px-3"><?= htmlspecialchars($user->name) ?></td>
                                <td class="py-2 px-3"><?= htmlspecialchars($user->email) ?></td>
                                <td class="py-2 px-3"><?= $user->created_at ?></td>
                                <td class="py-2 px-3">
                                    <a href="?delete=<?= $user->id ?>" onclick="return confirm('Delete user?')" class="text-red-600 hover:underline font-semibold">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <h2 class="text-lg font-semibold mb-2 mt-6">Repository Methods Test</h2>
            <div class="mb-6">
                <table class="min-w-full bg-white rounded shadow border border-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-3 text-left">Method</th>
                            <th class="py-2 px-3 text-left">Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($testResults as $method => $result) { ?>
                            <tr>
                                <td class="py-2 px-3 font-mono">$userRepo-><?= $method ?>(...)</td>
                                <td class="py-2 px-3 <?= $result === 'PASS' ? 'text-green-700' : 'text-red-700' ?> font-bold"><?= $result ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
