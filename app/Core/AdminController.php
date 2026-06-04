<?php

require_once __DIR__ . '/../admin/Models/OrderModel.php';
require_once __DIR__ . '/../admin/Models/ProduitCategoryModel.php';
require_once __DIR__ . '/../admin/Models/AccessControlModel.php';
require_once __DIR__ . '/MailConfigImporter.php';

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

            case 'actualites':
                return $this->actualites();

            case 'payment-schedules':
                return $this->paymentSchedules();

            case 'settings':
                return $this->settings();

            case 'profile':
                return $this->profile();

            case 'messaging':
                return $this->messaging();

            case 'newsletter':
                return $this->newsletter();

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
            $isMailTest = isset($_POST['test_mail_config']);

            if ($isMailTest) {
                require_once __DIR__ . '/../admin/Models/AdminMailboxModel.php';

                [$mailConfig, $configError] = $this->extractMailConfig($_POST);
                if ($configError !== null) {
                    $message = $configError;
                    $messageType = 'error';
                } else {
                    $result = (new AdminMailboxModel())->testConfig($mailConfig);
                    $message = $result['message'] ?? 'Test terminé.';
                    $messageType = !empty($result['success']) ? 'success' : 'error';
                }
            }

            if (!$isMailTest && $action === 'add_user') {
                $fullname = trim($_POST['fullname'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $role = trim($_POST['role'] ?? 'agent');
                $password = $_POST['password'] ?? '';
                [$mailConfig, $configError] = $this->extractMailConfig($_POST);

                if ($configError !== null) {
                    $message = $configError;
                    $messageType = 'error';
                } elseif ($fullname === '' || $email === '' || $password === '') {
                    $message = 'Veuillez remplir tous les champs obligatoires.';
                    $messageType = 'error';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $message = 'Adresse email invalide.';
                    $messageType = 'error';
                } elseif ($model->emailExists($email)) {
                    $message = 'Cet email existe déjà.';
                    $messageType = 'error';
                } else {
                    $ok = $model->addUser($fullname, $email, $password, $role, $mailConfig);

                    if ($ok) {
                        $message = 'Utilisateur créé avec succès.';
                    } else {
                        $message = 'Erreur lors de la création de l’utilisateur.';
                        $messageType = 'error';
                    }
                }
            }

            if (!$isMailTest && $action === 'toggle_status') {
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

            if (!$isMailTest && $action === 'edit_user') {
                $userId = (int) ($_POST['user_id'] ?? 0);
                $fullname = trim($_POST['fullname'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $role = trim($_POST['role'] ?? 'agent');
                $status = trim($_POST['status'] ?? 'active');
                [$mailConfig, $configError] = $this->extractMailConfig($_POST);

                if ($configError !== null) {
                    $message = $configError;
                    $messageType = 'error';
                } elseif ($userId <= 0 || $fullname === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $message = 'Veuillez vérifier les informations utilisateur.';
                    $messageType = 'error';
                } elseif ($model->emailExistsForAnotherUser($email, $userId)) {
                    $message = 'Cet email est déjà utilisé par un autre utilisateur.';
                    $messageType = 'error';
                } else {
                    $ok = $model->updateUser($userId, $fullname, $email, $role, $status, $mailConfig);
                    $message = $ok ? 'Utilisateur modifié avec succès.' : 'Impossible de modifier cet utilisateur.';
                    $messageType = $ok ? 'success' : 'error';
                }
            }

            if (!$isMailTest && $action === 'reset_password') {
                $userId = (int) ($_POST['user_id'] ?? 0);
                $password = (string) ($_POST['new_password'] ?? '');

                if ($userId <= 0 || strlen($password) < 6) {
                    $message = 'Le nouveau mot de passe doit contenir au moins 6 caractères.';
                    $messageType = 'error';
                } else {
                    $ok = $model->updatePassword($userId, $password);
                    $message = $ok ? 'Mot de passe réinitialisé.' : 'Impossible de réinitialiser le mot de passe.';
                    $messageType = $ok ? 'success' : 'error';
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

    private function actualites(): array
    {
        require_once __DIR__ . '/../admin/Models/ActualitesModel.php';

        $model = new ActualitesModel();

        $message = null;
        $messageType = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'add_actualite') {
                $title = trim($_POST['title'] ?? '');
                $subtitle = trim($_POST['subtitle'] ?? '');
                $category = trim($_POST['category'] ?? 'Actualité');
                $content = trim($_POST['content'] ?? '');
                $image = trim($_POST['image'] ?? '');
                $video = trim($_POST['video'] ?? '');
                $status = trim($_POST['status'] ?? 'published');
                $publishedAt = trim($_POST['published_at'] ?? '');

                if ($title === '' || $content === '') {
                    $message = 'Le titre et le contenu sont obligatoires.';
                    $messageType = 'error';
                } else {
                    $ok = $model->addActualite(
                        $title,
                        $subtitle,
                        $content,
                        $image,
                        $video,
                        $category,
                        $status,
                        $publishedAt
                    );

                    if ($ok) {
                        $message = 'Actualité ajoutée avec succès.';
                    } else {
                        $message = 'Impossible d’ajouter cette actualité.';
                        $messageType = 'error';
                    }
                }
            }
        }

        return [
            'currentPage' => 'actualites',
            'pageTitle' => 'Actualités',
            'view' => 'actualites.php',
            'actualites' => $model->getAllActualites(),
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

    private function profile(): array
    {
        require_once __DIR__ . '/../admin/Models/AuthModel.php';

        $model = new AuthModel();
        $message = null;
        $messageType = 'success';
        $adminId = (int) ($_SESSION['admin_id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentPassword = (string) ($_POST['current_password'] ?? '');
            $newPassword = (string) ($_POST['new_password'] ?? '');
            $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

            if ($adminId <= 0) {
                $message = 'Le compte de développement ne peut pas être modifié ici.';
                $messageType = 'error';
            } elseif ($newPassword !== $confirmPassword) {
                $message = 'Les deux nouveaux mots de passe ne correspondent pas.';
                $messageType = 'error';
            } elseif (strlen($newPassword) < 6) {
                $message = 'Le nouveau mot de passe doit contenir au moins 6 caractères.';
                $messageType = 'error';
            } else {
                $ok = $model->changePassword($adminId, $currentPassword, $newPassword);
                $message = $ok ? 'Mot de passe modifié avec succès.' : 'Mot de passe actuel incorrect.';
                $messageType = $ok ? 'success' : 'error';
            }
        }

        return [
            'currentPage' => 'profile',
            'pageTitle' => 'Mon profil',
            'view' => 'profile.php',
            'profileUser' => $adminId > 0 ? $model->getUserById($adminId) : null,
            'message' => $message,
            'messageType' => $messageType,
        ];
    }

    private function messaging(): array
    {
        require_once __DIR__ . '/../admin/Models/AdminMailboxModel.php';

        $model = new AdminMailboxModel();
        $adminId = (int) ($_SESSION['admin_id'] ?? 0);
        $message = null;
        $messageType = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $uid = (int) ($_POST['uid'] ?? 0);
            $body = trim($_POST['body'] ?? '');
            $result = $model->reply($adminId, $uid, $body);
            $message = $result['message'] ?? 'Réponse traitée.';
            $messageType = !empty($result['success']) ? 'success' : 'error';
        }

        $selectedUid = (int) ($_GET['uid'] ?? 0);
        $selectedMail = $selectedUid > 0 ? $model->getMessage($adminId, $selectedUid) : null;
        $messages = $model->getInbox($adminId);

        return [
            'currentPage' => 'messaging',
            'pageTitle' => 'Messagerie',
            'view' => 'messaging.php',
            'mailMessages' => $messages,
            'selectedMail' => $selectedMail,
            'mailStats' => $model->getStats($messages),
            'mailError' => $model->getLastError(),
            'message' => $message,
            'messageType' => $messageType,
        ];
    }

    private function extractMailConfig(array $source): array
    {
        $manualConfig = [
            'email_address' => trim($source['email_address'] ?? ''),
            'imap_host' => trim($source['imap_host'] ?? ''),
            'imap_port' => (int) ($source['imap_port'] ?? 993),
            'imap_encryption' => trim($source['imap_encryption'] ?? 'ssl'),
            'imap_username' => trim($source['imap_username'] ?? ''),
            'imap_password' => (string) ($source['imap_password'] ?? ''),
            'smtp_host' => trim($source['smtp_host'] ?? ''),
            'smtp_port' => (int) ($source['smtp_port'] ?? 465),
            'smtp_encryption' => trim($source['smtp_encryption'] ?? 'ssl'),
            'smtp_username' => trim($source['smtp_username'] ?? ''),
            'smtp_password' => (string) ($source['smtp_password'] ?? ''),
        ];

        [$jsonConfig, $error] = $this->readUploadedMailConfig();
        if ($error !== null) {
            return [$manualConfig, $error];
        }

        $config = $jsonConfig !== null
            ? array_merge($manualConfig, array_filter($jsonConfig, static fn ($value): bool => $value !== null && $value !== ''))
            : $manualConfig;

        return [$this->autoConfigureO2switch($config), null];
    }

    private function readUploadedMailConfig(): array
    {
        [$mobileConfig, $mobileError] = $this->readUploadedMobileConfig();
        if ($mobileError !== null || $mobileConfig !== null) {
            return [$mobileConfig, $mobileError];
        }

        $file = $_FILES['imap_config'] ?? null;
        if (!is_array($file) || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return [null, null];
        }

        if ((int) ($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return [null, 'Impossible de lire le fichier JSON uploadé.'];
        }

        if ((int) ($file['size'] ?? 0) > 64 * 1024) {
            return [null, 'Le fichier JSON de configuration ne doit pas dépasser 64 Ko.'];
        }

        $name = (string) ($file['name'] ?? '');
        if (strtolower(pathinfo($name, PATHINFO_EXTENSION)) !== 'json') {
            return [null, 'Le fichier de configuration doit être au format JSON.'];
        }

        $tmp = (string) ($file['tmp_name'] ?? '');
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            return [null, 'Fichier JSON invalide.'];
        }

        $raw = file_get_contents($tmp);
        @unlink($tmp);

        if ($raw === false || trim($raw) === '') {
            return [null, 'Fichier JSON vide ou illisible.'];
        }

        try {
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $e) {
            return [null, 'Le fichier uploadé n’est pas un JSON valide.'];
        }

        if (!is_array($data)) {
            return [null, 'Le JSON doit contenir un objet de configuration.'];
        }

        $allowed = [
            'email_address', 'imap_host', 'imap_port', 'imap_encryption', 'imap_username', 'imap_password',
            'smtp_host', 'smtp_port', 'smtp_encryption', 'smtp_username', 'smtp_password',
        ];

        $config = [];
        foreach ($allowed as $key) {
            $config[$key] = $data[$key] ?? null;
        }

        $config['config_source'] = 'json';

        return [$config, null];
    }

    private function readUploadedMobileConfig(): array
    {
        $file = $_FILES['mail_config_file'] ?? null;
        if (!is_array($file) || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return [null, null];
        }

        if ((int) ($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return [null, 'Impossible de lire le fichier mobileconfig uploadé.'];
        }

        if ((int) ($file['size'] ?? 0) > 256 * 1024) {
            return [null, 'Le fichier mobileconfig ne doit pas dépasser 256 Ko.'];
        }

        $name = (string) ($file['name'] ?? '');
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($extension, ['mobileconfig', 'xml'], true)) {
            return [null, 'Le fichier doit être au format .mobileconfig ou .xml.'];
        }

        $tmp = (string) ($file['tmp_name'] ?? '');
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            return [null, 'Fichier mobileconfig invalide.'];
        }

        $mime = '';
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $mime = (string) finfo_file($finfo, $tmp);
                finfo_close($finfo);
            }
        }

        $allowedMimes = [
            'text/xml',
            'application/xml',
            'application/x-apple-aspen-config',
            'application/octet-stream',
            'application/x-plist',
            'text/plain',
        ];
        if ($mime !== '' && !in_array($mime, $allowedMimes, true)) {
            @unlink($tmp);
            return [null, 'Type MIME du fichier mobileconfig non autorisé : ' . $mime . '.'];
        }

        try {
            $config = \App\Core\MailConfigImporter::parseMobileConfig($tmp);
        } catch (Throwable $e) {
            @unlink($tmp);
            return [null, $e->getMessage()];
        }

        @unlink($tmp);

        return [$config, null];
    }

    private function autoConfigureO2switch(array $config): array
    {
        $email = trim((string) ($config['email_address'] ?? ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $config;
        }

        $domain = substr(strrchr($email, '@') ?: '', 1);
        if ($domain === '') {
            return $config;
        }

        $host = 'mail.' . $domain;

        $config['imap_host'] = trim((string) ($config['imap_host'] ?? '')) !== '' ? $config['imap_host'] : $host;
        $config['imap_port'] = (int) ($config['imap_port'] ?? 0) > 0 ? $config['imap_port'] : 993;
        $config['imap_encryption'] = trim((string) ($config['imap_encryption'] ?? '')) !== '' ? $config['imap_encryption'] : 'ssl';
        $config['imap_username'] = trim((string) ($config['imap_username'] ?? '')) !== '' ? $config['imap_username'] : $email;
        $config['smtp_host'] = trim((string) ($config['smtp_host'] ?? '')) !== '' ? $config['smtp_host'] : $host;
        $config['smtp_port'] = (int) ($config['smtp_port'] ?? 0) > 0 ? $config['smtp_port'] : 465;
        $config['smtp_encryption'] = trim((string) ($config['smtp_encryption'] ?? '')) !== '' ? $config['smtp_encryption'] : 'ssl';
        $config['smtp_username'] = trim((string) ($config['smtp_username'] ?? '')) !== '' ? $config['smtp_username'] : $email;

        return $config;
    }

    private function newsletter(): array
    {
        require_once __DIR__ . '/../admin/Models/NewsletterModel.php';

        $model = new NewsletterModel();
        $message = null;
        $messageType = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'send_campaign') {
                [$attachments, $attachmentError] = $this->extractCampaignAttachments();

                if ($attachmentError !== null) {
                    $message = $attachmentError;
                    $messageType = 'error';
                } else {
                $result = $model->sendCampaign(
                    trim($_POST['subject'] ?? ''),
                    trim($_POST['content'] ?? ''),
                    trim($_POST['interest'] ?? ''),
                    $attachments
                );
                $message = $result['message'] ?? 'Campagne traitée.';
                $messageType = !empty($result['success']) ? 'success' : 'error';

                if (!empty($result['success']) && (int) ($result['failed'] ?? 0) > 0) {
                    $message .= ' Échecs : ' . (int) $result['failed'] . '.';
                }
                }
            }

            if ($action === 'update_status') {
                $subscriberId = (int) ($_POST['subscriber_id'] ?? 0);
                $status = trim($_POST['status'] ?? '');
                $ok = $model->updateStatus($subscriberId, $status);
                $message = $ok ? 'Statut abonné mis à jour.' : 'Impossible de modifier cet abonné.';
                $messageType = $ok ? 'success' : 'error';
            }
        }

        return [
            'currentPage' => 'newsletter',
            'pageTitle' => 'Newsletter',
            'view' => 'newsletter.php',
            'subscribers' => $model->getAll(),
            'newsletterStats' => $model->getStats(),
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

    private function extractCampaignAttachments(): array
    {
        $file = $_FILES['campaign_attachment'] ?? null;
        if (!is_array($file) || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return [[], null];
        }

        if ((int) ($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return [[], 'Impossible de lire la pièce jointe uploadée.'];
        }

        if ((int) ($file['size'] ?? 0) > 10 * 1024 * 1024) {
            return [[], 'La pièce jointe ne doit pas dépasser 10 Mo.'];
        }

        $tmp = (string) ($file['tmp_name'] ?? '');
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            return [[], 'Pièce jointe invalide.'];
        }

        $originalName = basename((string) ($file['name'] ?? 'piece-jointe'));
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx', 'txt'];

        if (!in_array($extension, $allowed, true)) {
            return [[], 'Format de pièce jointe non autorisé. Formats acceptés : PDF, image, Word, Excel, TXT.'];
        }

        return [[[
            'path' => $tmp,
            'name' => $originalName !== '' ? $originalName : 'piece-jointe.' . $extension,
        ]], null];
    }
}
