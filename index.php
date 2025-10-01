<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs - CRUD PHP</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="bi bi-people-fill me-2"></i>Gestion Utilisateurs</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-house me-1"></i>Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><i class="bi bi-people me-1"></i>Utilisateurs</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Messages d'alerte -->
        <div id="alertContainer"></div>

        <!-- Formulaire d'ajout/modification -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-person-plus me-2"></i>
                            <span id="formTitle">Ajouter un utilisateur</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="userForm">
                            <input type="hidden" id="userId" name="id">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                            <div class="mb-3">
                                <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="prenom" name="prenom" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="telephone" name="telephone">
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="bi bi-save me-2"></i>Enregistrer
                                </button>
                                <button type="button" class="btn btn-secondary" id="cancelBtn" onclick="resetForm()" style="display: none;">
                                    <i class="bi bi-x-circle me-2"></i>Annuler
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Liste des utilisateurs -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-list-ul me-2"></i>Liste des utilisateurs
                        </h5>
                        <div class="input-group" style="width: 300px;">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" id="searchInput" placeholder="Rechercher...">
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th width="60">Avatar</th>
                                        <th>Nom</th>
                                        <th>Prénom</th>
                                        <th>Email</th>
                                        <th>Téléphone</th>
                                        <th>Date création</th>
                                        <th width="140">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTableBody">
                                    <!-- Les utilisateurs seront chargés ici -->
                                </tbody>
                            </table>
                        </div>
                        <div id="noResults" class="text-center p-4" style="display: none;">
                            <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Aucun utilisateur trouvé</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer cet utilisateur ?</p>
                    <div class="alert alert-warning">
                        <i class="bi bi-info-circle me-2"></i>Cette action est irréversible.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="bi bi-trash me-2"></i>Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>


    <script>
        // ============================================
        // CONFIGURATION - Ajustez le chemin selon votre structure
        // ============================================

        // IMPORTANT: Changez ce chemin selon votre structure de dossier !
        // Si votre structure est: projet/index.php et projet/api/read.php → utilisez 'api/'
        // Si votre structure est différente, ajustez le chemin
        const API_BASE_URL = window.location.pathname.includes('/crud-php/') ? '/crud-php/api/' : 'api/';

        let users = [];
        let editingUserId = null;

        // ==========================================
        // INITIALISATION
        // ==========================================

        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Application démarrée');
            console.log('📁 Chemin API configuré:', API_BASE_URL);
            setupEventListeners();
            loadUsersFromAPI();
        });

        function setupEventListeners() {
            console.log('🔗 Configuration des événements...');

            // Soumission du formulaire
            const userForm = document.getElementById('userForm');
            if (userForm) {
                userForm.addEventListener('submit', handleFormSubmit);
            }

            // Recherche avec délai
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                let searchTimeout;
                searchInput.addEventListener('input', function(e) {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => handleSearch(e), 500);
                });
            }
        }

        // ==========================================
        // FONCTIONS API
        // ==========================================

        async function loadUsersFromAPI() {
            console.log('📡 Tentative de chargement des utilisateurs...');
            const fullUrl = API_BASE_URL + 'read.php';
            console.log('🔗 URL complète:', window.location.origin + fullUrl);

            showLoading(true);


            const response = await fetch(fullUrl);
            console.log('📨 Réponse reçue:', response.status, response.statusText);

            // Vérifier si la réponse est du JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('La réponse n\'est pas du JSON valide. Vérifiez que le fichier PHP existe et fonctionne.');
            }

            const data = await response.json();
            console.log('📊 Données reçues:', data);

            if (response.ok && data.records) {
                users = data.records;
                console.log('✅ Utilisateurs chargés:', users.length);
                loadUsers();
                showAlert(`${users.length} utilisateur(s) chargé(s) avec succès`, 'success');
                return true;
            } else if (response.status === 404) {
                users = [];
                console.log('📝 Aucun utilisateur trouvé');
                loadUsers();
                showAlert('Aucun utilisateur dans la base de données', 'info');
                return true;
            } else {
                throw new Error(data.message || 'Erreur inconnue');
            }


        }



        async function saveUser(userData, isUpdate = false) {
            const endpoint = isUpdate ? 'update.php' : 'create.php';
            const method = isUpdate ? 'PUT' : 'POST';

            console.log('💾 Sauvegarde utilisateur:', userData);
            setButtonLoading(true);

            try {
                const response = await fetch(API_BASE_URL + endpoint, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(userData)
                });

                const data = await response.json();

                if (response.ok) {
                    showAlert(data.message, 'success');
                    await loadUsersFromAPI();
                    return true;
                } else {
                    showAlert(data.message, 'danger');
                    return false;
                }
            } catch (error) {
                console.error('Erreur sauvegarde:', error);
                showAlert('Erreur lors de la sauvegarde', 'danger');
                return false;
            } finally {
                setButtonLoading(false);
            }
        }

        // ==========================================
        // GESTIONNAIRES D'ÉVÉNEMENTS
        // ==========================================

        async function handleFormSubmit(e) {
            e.preventDefault();
            console.log('📝 Soumission du formulaire');

            const formData = new FormData(e.target);
            const userData = {
                nom: formData.get('nom').trim(),
                prenom: formData.get('prenom').trim(),
                email: formData.get('email').trim(),
                telephone: formData.get('telephone').trim()
            };

            if (!userData.nom || !userData.prenom || !userData.email) {
                showAlert('Veuillez remplir tous les champs obligatoires', 'danger');
                return;
            }

            if (editingUserId) {
                userData.id = editingUserId;
                const success = await saveUser(userData, true);
                if (success) resetForm();
            } else {
                const success = await saveUser(userData, false);
                if (success) resetForm();
            }
        }
        // Remplacez votre fonction deleteUser actuelle par celle-ci:
        function deleteUser(id) {
            const user = users.find(u => u.id == id);
            if (!user) {
                showAlert('Utilisateur introuvable', 'danger');
                return;
            }

            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            const modalBody = document.querySelector('#deleteModal .modal-body');

            modalBody.innerHTML = `
        <div class="d-flex align-items-center mb-3">
            <div class="user-avatar me-3">
                ${user.prenom.charAt(0)}${user.nom.charAt(0)}
            </div>
            <div>
                <h6 class="mb-1">${user.prenom} ${user.nom}</h6>
                <small class="text-muted">${user.email}</small>
            </div>
        </div>
        <p>Êtes-vous sûr de vouloir supprimer cet utilisateur ?</p>
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Cette action est <strong>irréversible</strong>.
        </div>
    `;

            modal.show();

            const confirmBtn = document.getElementById('confirmDeleteBtn');
            const newConfirmBtn = confirmBtn.cloneNode(true);
            confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

            newConfirmBtn.onclick = async function() {
                const originalText = newConfirmBtn.innerHTML;

                try {
                    newConfirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Suppression...';
                    newConfirmBtn.disabled = true;

                    const success = await deleteUserFromAPI(id);

                    if (success) {
                        modal.hide();
                        await loadUsersFromAPI();
                        showAlert(`✅ Utilisateur "${user.prenom} ${user.nom}" supprimé avec succès`, 'success');
                    }
                } catch (error) {
                    showAlert('❌ Erreur: ' + error.message, 'danger');
                } finally {
                    newConfirmBtn.innerHTML = originalText;
                    newConfirmBtn.disabled = false;
                }
            };
        }

        
        async function deleteUserFromAPI(userId) {
            try {
                const response = await fetch(API_BASE_URL + 'delete.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        id: parseInt(userId)
                    })
                });

                const responseText = await response.text();
                const data = JSON.parse(responseText);

                if (response.ok && data.success) {
                    return true;
                } else {
                    throw new Error(data.message || 'Erreur inconnue');
                }
            } catch (error) {
                if (error.message.includes('Failed to fetch')) {
                    throw new Error('Impossible de se connecter au serveur');
                }
                throw error;
            }
        }

        async function handleSearch(e) {
            const searchTerm = e.target.value.trim();
            //console.log('🔍 Recherche:', searchTerm);

            if (!searchTerm) {
                loadUsers();
                return;
            }

            const filteredUsers = users.filter(user =>
                user.nom.toLowerCase().includes(searchTerm.toLowerCase()) ||
                user.prenom.toLowerCase().includes(searchTerm.toLowerCase()) ||
                user.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
                (user.telephone && user.telephone.includes(searchTerm))
            );

            loadUsers(filteredUsers);
        }

        // ==========================================
        // FONCTIONS D'INTERFACE
        // ==========================================

        function loadUsers(filteredUsers = null) {
            const tbody = document.getElementById('usersTableBody');
            const noResults = document.getElementById('noResults');
            const usersToShow = filteredUsers || users;

            console.log('📋 Affichage de', usersToShow.length, 'utilisateur(s)');

            if (usersToShow.length === 0) {
                tbody.innerHTML = '';
                noResults.style.display = 'block';
                return;
            }

            noResults.style.display = 'none';

            tbody.innerHTML = usersToShow.map(user => `
                <tr>
                    <td>
                        <div class="user-avatar">
                            ${user.prenom.charAt(0)}${user.nom.charAt(0)}
                        </div>
                    </td>
                    <td>${user.nom}</td>
                    <td>${user.prenom}</td>
                    <td>${user.email}</td>
                    <td>${user.telephone || '-'}</td>
                    <td>${formatDate(user.date_creation)}</td>
                    <td>
                        <button class="btn btn-outline-primary btn-sm btn-action" onclick="editUser(${user.id})" title="Modifier">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger btn-sm btn-action" onclick="deleteUser(${user.id})" title="Supprimer">
                            <i class="bi bi-trash"></i>
                        </button>
                        <button class="btn btn-outline-info btn-sm btn-action" onclick="viewUser(${user.id})" title="Voir détails">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function editUser(id) {
            const user = users.find(u => u.id == id);
            if (!user) return;

            editingUserId = id;

            document.getElementById('userId').value = user.id;
            document.getElementById('nom').value = user.nom;
            document.getElementById('prenom').value = user.prenom;
            document.getElementById('email').value = user.email;
            document.getElementById('telephone').value = user.telephone || '';

            document.getElementById('formTitle').textContent = 'Modifier l\'utilisateur';
            document.getElementById('submitBtn').innerHTML = '<i class="bi bi-save me-2"></i>Mettre à jour';
            document.getElementById('cancelBtn').style.display = 'block';
        }

        function viewUser(id) {
            const user = users.find(u => u.id == id);
            if (!user) return;

            const details = `
                <strong>Nom:</strong> ${user.nom}<br>
                <strong>Prénom:</strong> ${user.prenom}<br>
                <strong>Email:</strong> ${user.email}<br>
                <strong>Téléphone:</strong> ${user.telephone || 'Non renseigné'}<br>
                <strong>Date de création:</strong> ${formatDate(user.date_creation)}
            `;

            showAlert(details, 'info', 'Détails de l\'utilisateur', true);
        }

        function resetForm() {
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            editingUserId = null;

            document.getElementById('formTitle').textContent = 'Ajouter un utilisateur';
            document.getElementById('submitBtn').innerHTML = '<i class="bi bi-save me-2"></i>Enregistrer';
            document.getElementById('cancelBtn').style.display = 'none';
        }

        // ==========================================
        // FONCTIONS UTILITAIRES
        // ==========================================

        function showLoading(show) {
            const loadingIndicator = document.getElementById('loadingIndicator');
            if (loadingIndicator) {
                loadingIndicator.style.display = show ? 'block' : 'none';
            }
        }

        function setButtonLoading(loading) {
            const submitBtn = document.getElementById('submitBtn');
            const loadingSpan = submitBtn.querySelector('.loading');

            if (loading) {
                if (loadingSpan) loadingSpan.style.display = 'inline';
                submitBtn.disabled = true;
            } else {
                if (loadingSpan) loadingSpan.style.display = 'none';
                submitBtn.disabled = false;
            }
        }

        function showAlert(message, type = 'info', title = null, isHtml = false) {
            const alertContainer = document.getElementById('alertContainer');
            const alertId = 'alert-' + Date.now();

            const iconMap = {
                'success': 'bi-check-circle',
                'danger': 'bi-exclamation-triangle',
                'warning': 'bi-exclamation-triangle',
                'info': 'bi-info-circle'
            };

            const icon = iconMap[type] || 'bi-info-circle';

            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert" id="${alertId}">
                    ${title ? `<h6 class="alert-heading"><i class="${icon} me-2"></i>${title}</h6>` : ''}
                    ${isHtml ? message : `<i class="${icon} me-2"></i>${message}`}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

            alertContainer.insertAdjacentHTML('beforeend', alertHtml);

            // Auto-remove après 8 secondes
            setTimeout(() => {
                const alert = document.getElementById(alertId);
                if (alert) {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    bsAlert.close();
                }
            }, 8000);
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    </script>