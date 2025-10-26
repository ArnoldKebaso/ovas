<?php
require_once 'initialize.php';
require_once 'classes/PetsModel.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$userId = $_SESSION['user_id'];
$petsModel = new PetsModel();
$action = $_GET['action'] ?? 'list';
$petId = $_GET['id'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Invalid security token. Please try again.';
    } else {
        try {
            if ($_POST['action'] === 'create') {
                $petData = [
                    'user_id' => $userId,
                    'name' => trim($_POST['name']),
                    'species' => $_POST['species'],
                    'breed' => trim($_POST['breed']),
                    'age' => (int)$_POST['age'],
                    'weight' => !empty($_POST['weight']) ? (float)$_POST['weight'] : null,
                    'color' => trim($_POST['color']),
                    'gender' => $_POST['gender'],
                    'microchip_number' => trim($_POST['microchip_number']),
                    'medical_notes' => trim($_POST['medical_notes']),
                    'is_active' => 1
                ];
                
                $newPetId = $petsModel->create($petData);
                if ($newPetId) {
                    $success = "Pet registered successfully!";
                    $action = 'list';
                } else {
                    $error = "Failed to register pet. Please try again.";
                }
            } elseif ($_POST['action'] === 'update') {
                $petData = [
                    'name' => trim($_POST['name']),
                    'species' => $_POST['species'],
                    'breed' => trim($_POST['breed']),
                    'age' => (int)$_POST['age'],
                    'weight' => !empty($_POST['weight']) ? (float)$_POST['weight'] : null,
                    'color' => trim($_POST['color']),
                    'gender' => $_POST['gender'],
                    'microchip_number' => trim($_POST['microchip_number']),
                    'medical_notes' => trim($_POST['medical_notes'])
                ];
                
                if ($petsModel->update($petId, $petData)) {
                    $success = "Pet information updated successfully!";
                    $action = 'list';
                } else {
                    $error = "Failed to update pet information. Please try again.";
                }
            }
        } catch (Exception $e) {
            $error = "An error occurred: " . $e->getMessage();
        }
    }
}

// Get pets for list view
if ($action === 'list') {
    $pets = $petsModel->getUserPets($userId);
}

// Get specific pet for edit view
if (($action === 'edit' || $action === 'view') && $petId) {
    $pet = $petsModel->find($petId);
    if (!$pet || $pet['user_id'] != $userId) {
        $error = "Pet not found or access denied.";
        $action = 'list';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Pets - OVAS</title>
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>">
    
    <!-- Bootstrap CSS -->
    <link href="plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="plugins/fontawesome-free/css/all.min.css" rel="stylesheet">
    <!-- AdminLTE -->
    <link href="libs/style.css" rel="stylesheet">
    
    <style>
        .pet-card {
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border: none;
            margin-bottom: 1.5rem;
            transition: transform 0.2s;
        }
        .pet-card:hover {
            transform: translateY(-3px);
        }
        .pet-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
        }
        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .form-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .species-icon {
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }
        .pet-info-row {
            border-bottom: 1px solid #e9ecef;
            padding: 0.75rem 0;
        }
        .pet-info-row:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-paw"></i> OVAS
            </a>
            <div class="navbar-nav ml-auto">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a class="nav-link" href="appointment.php">
                    <i class="fas fa-calendar-plus"></i> Book Appointment
                </a>
                <a class="nav-link active" href="my_pets.php">
                    <i class="fas fa-dog"></i> My Pets
                </a>
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if ($action === 'list'): ?>
            <!-- Pet List View -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-paw text-primary"></i> My Pets</h2>
                <a href="?action=add" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Pet
                </a>
            </div>

            <?php if (!empty($pets)): ?>
                <div class="row">
                    <?php foreach ($pets as $pet): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card pet-card">
                                <div class="card-body text-center">
                                    <div class="pet-avatar mx-auto mb-3">
                                        <i class="fas fa-<?php echo strtolower($pet['species']) === 'cat' ? 'cat' : 'dog'; ?>"></i>
                                    </div>
                                    <h5 class="card-title"><?php echo htmlspecialchars($pet['name']); ?></h5>
                                    <p class="text-muted mb-3">
                                        <span class="species-icon">
                                            <?php if (strtolower($pet['species']) === 'cat'): ?>
                                                <i class="fas fa-cat text-purple"></i>
                                            <?php else: ?>
                                                <i class="fas fa-dog text-brown"></i>
                                            <?php endif; ?>
                                        </span>
                                        <?php echo htmlspecialchars($pet['species']); ?>
                                        <?php if ($pet['breed']): ?>
                                            • <?php echo htmlspecialchars($pet['breed']); ?>
                                        <?php endif; ?>
                                    </p>
                                    <div class="row text-center mb-3">
                                        <div class="col-6">
                                            <small class="text-muted">Age</small>
                                            <div class="font-weight-bold"><?php echo $pet['age']; ?> years</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Gender</small>
                                            <div class="font-weight-bold">
                                                <i class="fas fa-<?php echo $pet['gender'] === 'male' ? 'mars' : 'venus'; ?>"></i>
                                                <?php echo ucfirst($pet['gender']); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="btn-group btn-group-sm w-100">
                                        <a href="?action=view&id=<?php echo $pet['id']; ?>" class="btn btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="?action=edit&id=<?php echo $pet['id']; ?>" class="btn btn-outline-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <button class="btn btn-outline-danger" onclick="confirmDelete(<?php echo $pet['id']; ?>, '<?php echo htmlspecialchars($pet['name']); ?>')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-paw fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No pets registered yet</h4>
                    <p class="text-muted mb-4">Add your first pet to start booking appointments!</p>
                    <a href="?action=add" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus"></i> Add Your First Pet
                    </a>
                </div>
            <?php endif; ?>

        <?php elseif ($action === 'view' && isset($pet)): ?>
            <!-- Pet View -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-eye text-info"></i> Pet Details</h2>
                <div>
                    <a href="?action=edit&id=<?php echo $pet['id']; ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="?action=list" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="pet-avatar mx-auto mb-3" style="width: 120px; height: 120px; font-size: 3rem;">
                                <i class="fas fa-<?php echo strtolower($pet['species']) === 'cat' ? 'cat' : 'dog'; ?>"></i>
                            </div>
                            <h3><?php echo htmlspecialchars($pet['name']); ?></h3>
                            <p class="text-muted">
                                <?php echo htmlspecialchars($pet['species']); ?>
                                <?php if ($pet['breed']): ?>
                                    • <?php echo htmlspecialchars($pet['breed']); ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Pet Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="pet-info-row">
                                <div class="row">
                                    <div class="col-sm-4 font-weight-bold">Age:</div>
                                    <div class="col-sm-8"><?php echo $pet['age']; ?> years old</div>
                                </div>
                            </div>
                            <div class="pet-info-row">
                                <div class="row">
                                    <div class="col-sm-4 font-weight-bold">Gender:</div>
                                    <div class="col-sm-8">
                                        <i class="fas fa-<?php echo $pet['gender'] === 'male' ? 'mars' : 'venus'; ?>"></i>
                                        <?php echo ucfirst($pet['gender']); ?>
                                    </div>
                                </div>
                            </div>
                            <?php if ($pet['weight']): ?>
                                <div class="pet-info-row">
                                    <div class="row">
                                        <div class="col-sm-4 font-weight-bold">Weight:</div>
                                        <div class="col-sm-8"><?php echo $pet['weight']; ?> kg</div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($pet['color']): ?>
                                <div class="pet-info-row">
                                    <div class="row">
                                        <div class="col-sm-4 font-weight-bold">Color:</div>
                                        <div class="col-sm-8"><?php echo htmlspecialchars($pet['color']); ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($pet['microchip_number']): ?>
                                <div class="pet-info-row">
                                    <div class="row">
                                        <div class="col-sm-4 font-weight-bold">Microchip:</div>
                                        <div class="col-sm-8"><?php echo htmlspecialchars($pet['microchip_number']); ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($pet['medical_notes']): ?>
                                <div class="pet-info-row">
                                    <div class="row">
                                        <div class="col-sm-4 font-weight-bold">Medical Notes:</div>
                                        <div class="col-sm-8"><?php echo nl2br(htmlspecialchars($pet['medical_notes'])); ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="pet-info-row">
                                <div class="row">
                                    <div class="col-sm-4 font-weight-bold">Registered:</div>
                                    <div class="col-sm-8"><?php echo date('F j, Y', strtotime($pet['created_at'])); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif (in_array($action, ['add', 'edit'])): ?>
            <!-- Pet Form -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-<?php echo $action === 'add' ? 'plus' : 'edit'; ?> text-primary"></i>
                    <?php echo $action === 'add' ? 'Add New Pet' : 'Edit Pet'; ?>
                </h2>
                <a href="?action=list" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" id="petForm">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="action" value="<?php echo $action === 'add' ? 'create' : 'update'; ?>">

                                <div class="form-section">
                                    <h5 class="mb-3"><i class="fas fa-info-circle"></i> Basic Information</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Pet Name *</label>
                                                <input type="text" class="form-control" id="name" name="name" 
                                                       value="<?php echo isset($pet) ? htmlspecialchars($pet['name']) : ''; ?>" 
                                                       required maxlength="100">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="species">Species *</label>
                                                <select class="form-control" id="species" name="species" required>
                                                    <option value="">Select Species</option>
                                                    <option value="Dog" <?php echo (isset($pet) && $pet['species'] === 'Dog') ? 'selected' : ''; ?>>Dog</option>
                                                    <option value="Cat" <?php echo (isset($pet) && $pet['species'] === 'Cat') ? 'selected' : ''; ?>>Cat</option>
                                                    <option value="Bird" <?php echo (isset($pet) && $pet['species'] === 'Bird') ? 'selected' : ''; ?>>Bird</option>
                                                    <option value="Rabbit" <?php echo (isset($pet) && $pet['species'] === 'Rabbit') ? 'selected' : ''; ?>>Rabbit</option>
                                                    <option value="Other" <?php echo (isset($pet) && $pet['species'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="breed">Breed</label>
                                                <input type="text" class="form-control" id="breed" name="breed" 
                                                       value="<?php echo isset($pet) ? htmlspecialchars($pet['breed']) : ''; ?>" 
                                                       maxlength="100">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="color">Color</label>
                                                <input type="text" class="form-control" id="color" name="color" 
                                                       value="<?php echo isset($pet) ? htmlspecialchars($pet['color']) : ''; ?>" 
                                                       maxlength="50">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <h5 class="mb-3"><i class="fas fa-chart-line"></i> Physical Details</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="age">Age (years) *</label>
                                                <input type="number" class="form-control" id="age" name="age" 
                                                       value="<?php echo isset($pet) ? $pet['age'] : ''; ?>" 
                                                       required min="0" max="30">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="weight">Weight (kg)</label>
                                                <input type="number" class="form-control" id="weight" name="weight" 
                                                       value="<?php echo isset($pet) ? $pet['weight'] : ''; ?>" 
                                                       step="0.1" min="0" max="200">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="gender">Gender *</label>
                                                <select class="form-control" id="gender" name="gender" required>
                                                    <option value="">Select Gender</option>
                                                    <option value="male" <?php echo (isset($pet) && $pet['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                                                    <option value="female" <?php echo (isset($pet) && $pet['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <h5 class="mb-3"><i class="fas fa-clipboard"></i> Additional Information</h5>
                                    
                                    <div class="form-group">
                                        <label for="microchip_number">Microchip Number</label>
                                        <input type="text" class="form-control" id="microchip_number" name="microchip_number" 
                                               value="<?php echo isset($pet) ? htmlspecialchars($pet['microchip_number']) : ''; ?>" 
                                               maxlength="50">
                                        <small class="form-text text-muted">15-digit microchip identification number</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="medical_notes">Medical Notes</label>
                                        <textarea class="form-control" id="medical_notes" name="medical_notes" rows="4" 
                                                  maxlength="1000"><?php echo isset($pet) ? htmlspecialchars($pet['medical_notes']) : ''; ?></textarea>
                                        <small class="form-text text-muted">Allergies, medications, special conditions, etc.</small>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save"></i> 
                                        <?php echo $action === 'add' ? 'Register Pet' : 'Update Pet'; ?>
                                    </button>
                                    <a href="?action=list" class="btn btn-secondary btn-lg ml-2">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="petNameToDelete"></strong>?</p>
                    <p class="text-danger">This action cannot be undone!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete Pet</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <script>
    let petToDelete = null;

    function confirmDelete(petId, petName) {
        petToDelete = petId;
        $('#petNameToDelete').text(petName);
        $('#deleteModal').modal('show');
    }

    $('#confirmDeleteBtn').click(function() {
        if (petToDelete) {
            $.ajax({
                url: 'api/pets.php?action=delete',
                method: 'POST',
                data: {
                    id: petToDelete,
                    csrf_token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Failed to delete pet: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while deleting the pet.');
                }
            });
        }
        $('#deleteModal').modal('hide');
    });

    // Form validation
    $('#petForm').submit(function(e) {
        const name = $('#name').val().trim();
        const species = $('#species').val();
        const age = $('#age').val();
        const gender = $('#gender').val();

        if (!name || !species || !age || !gender) {
            e.preventDefault();
            alert('Please fill in all required fields (marked with *)');
            return false;
        }

        if (age < 0 || age > 30) {
            e.preventDefault();
            alert('Please enter a valid age between 0 and 30 years');
            return false;
        }
    });
    </script>
</body>
</html>