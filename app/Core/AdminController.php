<?php

require_once __DIR__ . '/../admin/Models/OrderModel.php';
require_once __DIR__ . '/../admin/Models/ProduitCategoryModel.php';
require_once __DIR__ . '/../admin/Models/AccessControlModel.php';

class AdminController
{
    public function handle(string $route): array
    {
        $route = trim($route) !== '' ? trim($route) : 'dashboard';
        $accessModel = new AccessControlModel();
        $role = $_SESSION['admin_role'] ?? 'agent';

        if (!$accessModel->canAccess((string) $role, $route)) {
            return [
                'currentPage' => $route,
                'pageTitle' => 'Accès refusé',
                'view' => 'access-denied.php',
                'message' => null,
                'messageType' => null,
            ];
        }

        switch ($route) {
            case 'dashboard':
                return $this->dashboard();

            case 'auth':
                return $this->auth();

            case 'access-control':
                return $this->accessControl();

            case 'clients':
                return $this->clients();

            case 'products':
                return $this->products();

            case 'orders':
                return $this->orders();

            case 'programme-immo':
                return $this->programmeImmo();

            case 'payment-schedules':
                return $this->paymentSchedules();

            case 'settings':
                return $this->settings();

            case 'employees':
                return $this->employees();

            case 'notifications':
                return $this->notifications();

            default:
                return $this->dashboard();
        }
    }

    private function dashboard(): array
    {
        return [
            'currentPage' => 'dashboard',
            'pageTitle' => 'Tableau de bord',
            'view' => 'dashboardv2.php',
            'message' => null,
            'messageType' => null,
        ];
    }

    private function auth(): array
    {
        require_once __DIR__ . '/../admin/Models/AuthModel.php';

        $model = new AuthModel();

        $message = null;
        $messageType = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? 'add_user';

            if ($action === 'add_user') {
                $fullname = trim($_POST['fullname'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $role = trim($_POST['role'] ?? 'agent');
                $password = $_POST['password'] ?? '';

                if ($fullname === '' || $email === '' || $password === '') {
                    $message = 'Veuillez remplir tous les champs obligatoires.';
                    $messageType = 'error';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $message = 'Adresse email invalide.';
                    $messageType = 'error';
                } elseif ($model->emailExists($email)) {
                    $message = 'Cet email existe déjà.';
                    $messageType = 'error';
                } else {
                    $ok = $model->addUser($fullname, $email, $password, $role);

                    if ($ok) {
                        $message = 'Utilisateur créé avec succès.';
                    } else {
                        $message = 'Erreur lors de la création de l’utilisateur.';
                        $messageType = 'error';
                    }
                }
            }

            if ($action === 'toggle_status') {
                $userId = (int) ($_POST['user_id'] ?? 0);
                $status = trim($_POST['status'] ?? '');

                if ($userId <= 0 || $status === '') {
                    $message = 'Action invalide.';
                    $messageType = 'error';
                } else {
                    $ok = $model->updateStatus($userId, $status);

                    if ($ok) {
                        $message = 'Statut mis à jour.';
                    } else {
                        $message = 'Impossible de modifier le statut.';
                        $messageType = 'error';
                    }
                }
            }
        }

        return [
            'currentPage' => 'auth',
            'pageTitle' => 'Authentification & rôles',
            'view' => 'auth.php',
            'users' => $model->getAllUsers(),
            'roles' => (new AccessControlModel())->getRoles(),
            'message' => $message,
            'messageType' => $messageType,
        ];
    }

    private function accessControl(): array
    {
        $model = new AccessControlModel();

        $message = null;
        $messageType = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? 'save_access';

            if ($action === 'add_role') {
                $ok = $model->addRole(trim($_POST['role_key'] ?? ''), trim($_POST['role_label'] ?? ''));
                $message = $ok ? 'Profil ajouté avec succès.' : 'Impossible d’ajouter ce profil.';
                $messageType = $ok ? 'success' : 'error';
            } else {
                $ok = $model->saveMatrix($_POST['access'] ?? []);

                if ($ok) {
                    $model->loadSessionFeatures((string) ($_SESSION['admin_role'] ?? 'agent'));
                    $message = 'Les accès par profil ont été mis à jour.';
                } else {
                    $message = 'Impossible de mettre à jour les accès.';
                    $messageType = 'error';
                }
            }
        }

        return [
            'currentPage' => 'access-control',
            'pageTitle' => 'Gestion des accès',
            'view' => 'access-control.php',
            'roles' => $model->getRoles(),
            'pages' => $model->getPages(),
            'accessMatrix' => $model->getMatrix(),
            'message' => $message,
            'messageType' => $messageType,
        ];
    }

    private function clients(): array
    {
        require_once __DIR__ . '/../admin/Models/ClientModel.php';

        $model = new ClientModel();

        $message = null;
        $messageType = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'add_client') {
                $nom = trim($_POST['nom'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $telephone = trim($_POST['telephone'] ?? '');

                if ($nom === '' || $email === '' || $telephone === '') {
                    $message = 'Veuillez remplir tous les champs.';
                    $messageType = 'error';
                } else {
                    $ok = $model->addClient($nom, $email, $telephone);

                    if ($ok) {
                        $message = 'Client ajouté avec succès.';
                    } else {
                        $message = 'Erreur lors de l’ajout du client.';
                        $messageType = 'error';
                    }
                }
            }
        }

        return [
            'currentPage' => 'clients',
            'pageTitle' => 'Clients & contacts',
            'view' => 'clients.php',
            'clients' => $model->getAllClients(),
            'message' => $message,
            'messageType' => $messageType,
        ];
    }

    private function products(): array
    {
        $model = new ProduitCategoryModel();

        $message = null;
        $messageType = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'add_category') {
                $nom = trim($_POST['nom'] ?? '');
                $description = trim($_POST['description'] ?? '');

                if ($nom === '') {
                    $message = 'Le nom de la catégorie est obligatoire.';
                    $messageType = 'error';
                } elseif ($model->categoryExistsByName($nom)) {
                    $message = 'Cette catégorie existe déjà.';
                    $messageType = 'error';
                } else {
                    $ok = $model->addCategory($nom, $description);

                    if ($ok) {
                        $message = 'Catégorie ajoutée avec succès.';
                    } else {
                        $message = 'Erreur lors de l’ajout de la catégorie.';
                        $messageType = 'error';
                    }
                }
            }

            if ($action === 'add_product') {
                $categorieId = (int) ($_POST['categorie_id'] ?? 0);
                $nom = trim($_POST['nom'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $prix = (float) ($_POST['prix'] ?? 0);
                $ancienPrix = ($_POST['ancien_prix'] ?? '') !== '' ? (float) $_POST['ancien_prix'] : null;
                $image = trim($_POST['image'] ?? '') ?: null;
                $note = ($_POST['note'] ?? '') !== '' ? (float) $_POST['note'] : null;
                $nbAvis = (int) ($_POST['nb_avis'] ?? 0);
                $typeMedia = trim($_POST['type_media'] ?? 'image');
                $mediaSrc = trim($_POST['media_src'] ?? '') ?: null;
                $actif = isset($_POST['actif']) ? 1 : 0;

                if ($categorieId <= 0 || $nom === '' || $prix < 0) {
                    $message = 'Veuillez remplir correctement les champs du produit.';
                    $messageType = 'error';
                } elseif ($model->getCategoryById($categorieId) === null) {
                    $message = 'La catégorie sélectionnée est invalide.';
                    $messageType = 'error';
                } elseif ($model->productExistsByName($nom)) {
                    $message = 'Ce produit existe déjà.';
                    $messageType = 'error';
                } else {
                    $ok = $model->addProduct(
                        $categorieId,
                        $nom,
                        $description,
                        $prix,
                        $ancienPrix,
                        $image,
                        $note,
                        $nbAvis,
                        $typeMedia,
                        $mediaSrc,
                        $actif
                    );

                    if ($ok) {
                        $message = 'Produit ajouté avec succès.';
                    } else {
                        $message = 'Erreur lors de l’ajout du produit.';
                        $messageType = 'error';
                    }
                }
            }
        }

        return [
            'currentPage' => 'products',
            'pageTitle' => 'Produits & stock',
            'view' => 'products.php',
            'categories' => $model->getAllCategories(),
            'products' => $model->getAllProducts(),
            'message' => $message,
            'messageType' => $messageType,
        ];
    }

    private function orders(): array
    {
        $model = new OrderModel();

        $message = null;
        $messageType = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'quote_status') {
                $quoteId = (int) ($_POST['quote_id'] ?? 0);
                $status = trim($_POST['status'] ?? '');
                $ok = $model->updateQuoteStatus($quoteId, $status);
                $message = $ok ? 'Statut du devis mis à jour.' : 'Impossible de modifier le statut du devis.';
                $messageType = $ok ? 'success' : 'error';
            }

            if ($action === 'validate_quote') {
                $quoteId = (int) ($_POST['quote_id'] ?? 0);
                $ok = $model->validateQuote($quoteId);
                $message = $ok ? 'Devis validé et commande créée.' : 'Impossible de valider ce devis.';
                $messageType = $ok ? 'success' : 'error';
            }

            if ($action === 'order_status') {
                $orderId = (int) ($_POST['order_id'] ?? 0);
                $status = trim($_POST['status'] ?? '');
                $ok = $model->updateOrderStatus($orderId, $status);
                $message = $ok ? 'Statut de commande mis à jour.' : 'Impossible de modifier le statut de commande.';
                $messageType = $ok ? 'success' : 'error';
            }

            if ($action === 'payment_status') {
                $orderId = (int) ($_POST['order_id'] ?? 0);
                $status = trim($_POST['status'] ?? '');
                $ok = $model->updatePaymentStatus($orderId, $status);
                $message = $ok ? 'Statut de paiement mis à jour.' : 'Impossible de modifier le paiement.';
                $messageType = $ok ? 'success' : 'error';
            }
        }

        return [
            'currentPage' => 'orders',
            'pageTitle' => 'Commandes & devis',
            'view' => 'orders.php',
            'orders' => $model->getAllOrders(),
            'quotes' => $model->getAllQuotes(),
            'message' => $message,
            'messageType' => $messageType,
        ];
    }
    private function programmeImmo(): array
    {
        require_once __DIR__ . '/../admin/Models/ImmoProgramModel.php';

        $model = new ImmoProgramModel();
        $message = null;
        $messageType = 'success';

        // ── Réponse JSON pour le dialog "Voir" ──────────────────────────────
        if (($_GET['action'] ?? '') === 'get_json' && isset($_GET['id'])) {
            $id = (int) $_GET['id'];

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'adhesion' => $model->getAdhesionById($id),
                'notes' => $model->getNotes($id),
                'contract' => $model->getContract($id),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        // ── Actions POST ─────────────────────────────────────────────────────
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $adhesionId = (int) ($_POST['adhesion_id'] ?? 0);

            if ($action === 'update_status') {
                $status = trim($_POST['status'] ?? '');
                $ok = $model->updateStatus($adhesionId, $status);
                $message = $ok ? 'Statut de l\'adhésion mis à jour.' : 'Impossible de modifier cette adhésion.';
                $messageType = $ok ? 'success' : 'error';
            }

            if ($action === 'add_note') {
                $note = trim($_POST['note'] ?? '');
                $adminId = isset($_SESSION['admin_id']) ? (int) $_SESSION['admin_id'] : null;
                $ok = $model->addNote($adhesionId, $adminId, $note);
                $message = $ok ? 'Note ajoutée au dossier.' : 'Impossible d\'ajouter la note.';
                $messageType = $ok ? 'success' : 'error';
            }

            if ($action === 'delete') {
                $ok = $model->deleteAdhesion($adhesionId);
                $message = $ok ? 'Adhésion supprimée.' : 'Impossible de supprimer cette adhésion.';
                $messageType = $ok ? 'success' : 'error';
            }

            if ($action === 'save_contract') {
                $ok = $model->saveContract($adhesionId, trim($_POST['contract_content'] ?? ''));
                $message = $ok ? 'Contrat enregistré.' : 'Impossible d\'enregistrer le contrat.';
                $messageType = $ok ? 'success' : 'error';
            }

            if ($action === 'send_contract') {
                $ok = $model->sendContractByEmail($adhesionId);
                $message = $ok ? 'Contrat envoyé par mail.' : 'Impossible d\'envoyer le contrat.';
                $messageType = $ok ? 'success' : 'error';
            }
        }

        // ── Données pour la vue ──────────────────────────────────────────────
        $selectedId = (int) ($_GET['id'] ?? 0);

        return [
            'currentPage' => 'programme-immo',
            'pageTitle' => 'Programme Immo',
            'view' => 'programme-immo.php',
            'adhesions' => $model->getAllAdhesions(),
            'stats' => $model->getStats(),
            'selectedAdhesion' => $selectedId > 0 ? $model->getAdhesionById($selectedId) : null,
            'selectedContract' => $selectedId > 0 ? $model->getContract($selectedId) : null,
            'selectedNotes' => $selectedId > 0 ? $model->getNotes($selectedId) : [],
            'message' => $message,
            'messageType' => $messageType,
        ];
    }

    private function paymentSchedules(): array
    {
        require_once __DIR__ . '/../admin/Models/PaymentScheduleModel.php';

        $model = new PaymentScheduleModel();
        $message = null;
        $messageType = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $scheduleId = (int) ($_POST['schedule_id'] ?? 0);

            if ($action === 'mark_paid') {
                $ok = $model->markPaid(
                    $scheduleId,
                    trim($_POST['payment_method'] ?? ''),
                    trim($_POST['note'] ?? '')
                );
                $message = $ok ? 'Échéance marquée comme payée.' : 'Impossible de marquer cette échéance comme payée.';
                $messageType = $ok ? 'success' : 'error';
            }

            if ($action === 'mark_pending') {
                $ok = $model->markPending($scheduleId);
                $message = $ok ? 'Échéance remise en attente.' : 'Impossible de modifier cette échéance.';
                $messageType = $ok ? 'success' : 'error';
            }

            if ($action === 'send_reminder') {
                $adhesionId = (int) ($_POST['adhesion_id'] ?? 0);
                $ok = $model->sendReminder($adhesionId);
                $message = $ok ? 'Relance envoyée au client.' : 'Impossible d’envoyer la relance.';
                $messageType = $ok ? 'success' : 'error';
            }
        }

        $status = trim($_GET['status'] ?? '');
        $allowedStatuses = ['pending', 'late', 'paid'];
        $statusFilter = in_array($status, $allowedStatuses, true) ? $status : null;

        return [
            'currentPage' => 'payment-schedules',
            'pageTitle' => 'Échéances de paiement',
            'view' => 'payment-schedules.php',
            'schedules' => $model->getSchedules($statusFilter),
            'paymentClients' => $model->getClientSummaries($statusFilter),
            'scheduleStats' => $model->getStats(),
            'statusFilter' => $statusFilter,
            'message' => $message,
            'messageType' => $messageType,
        ];
    }

    private function settings(): array
    {
        require_once __DIR__ . '/../admin/Models/SettingsModel.php';

        $model = new SettingsModel();
        $message = null;
        $messageType = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'save_settings') {
                $settings = $_POST['settings'] ?? [];
                $ok = is_array($settings) && $model->updateSettings($settings);
                $message = $ok ? 'Paramètres enregistrés avec succès.' : 'Impossible d’enregistrer les paramètres.';
                $messageType = $ok ? 'success' : 'error';
            }
        }

        return [
            'currentPage' => 'settings',
            'pageTitle' => 'Paramétrage général',
            'view' => 'settings.php',
            'settingGroups' => $model->getGroupedSettings(),
            'message' => $message,
            'messageType' => $messageType,
        ];
    }

    private function employees(): array
    {
        require_once __DIR__ . '/../admin/Models/AuthModel.php';

        $model = new AuthModel();

        return [
            'currentPage' => 'employees',
            'pageTitle' => 'Personnel',
            'view' => 'employees.php',
            'users' => $model->getAllUsers(),
            'roles' => (new AccessControlModel())->getRoles(),
            'message' => null,
            'messageType' => null,
        ];
    }

    private function notifications(): array
    {
        return [
            'currentPage' => 'notifications',
            'pageTitle' => 'Notifications',
            'view' => 'notifications.php',
            'message' => null,
            'messageType' => null,
        ];
    }
}
