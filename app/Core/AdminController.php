<?php

require_once __DIR__ . '/../admin/Models/OrderModel.php';
require_once __DIR__ . '/../admin/Models/ProduitCategoryModel.php';
require_once __DIR__ . '/../admin/Models/NotificationModel.php';
require_once __DIR__ . '/../admin/Models/ProgrammeImmobilierModel.php';
require_once __DIR__ . '/../admin/Models/AuthModel.php';

class AdminController
{
    public function handle(string $route): array
    {
        switch ($route) {
            case 'dashboard':
                return $this->dashboard();

            case 'auth':
                return $this->auth();

            case 'clients':
                return $this->clients();

            case 'products':
                return $this->products();

            case 'orders':
                return $this->orders();

            case 'employees':
                return $this->employees();

            case 'notifications':
                return $this->notifications();

            case 'inscriptions':
                return $this->inscriptions();

            case 'programmes-immobiliers':
                return $this->programmesImmobiliers();

            default:
                return $this->dashboard();
        }
    }

    private function dashboard(): array
    {
        return [
            'currentPage' => 'dashboard',
            'pageTitle'   => 'Tableau de bord',
            'view'        => 'dashboardv2.php',
            'message'     => null,
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
            'pageTitle'   => 'Authentification & rôles',
            'view'        => 'auth.php',
            'users'       => $model->getAllUsers(),
            'message'     => $message,
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
                'pageTitle'   => 'Clients & contacts',
                'view'        => 'clients.php',
                'clients'     => $model->getAllClients(),
                'message'     => $message,
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
            'pageTitle'   => 'Produits & stock',
            'view'        => 'products.php',
            'categories'  => $model->getAllCategories(),
            'products'    => $model->getAllProducts(),
            'message'     => $message,
            'messageType' => $messageType,
        ];
    }

    private function orders(): array
    {
        $model = new OrderModel();

        return [
            'currentPage' => 'orders',
            'pageTitle'   => 'Commandes & devis',
            'view'        => 'orders.php',
            'orders'      => $model->getAllOrders(),
            'quotes'      => $model->getAllQuotes(),
            'message'     => null,
            'messageType' => null,
        ];
    }

    private function employees(): array
    {
        return [
            'currentPage' => 'employees',
            'pageTitle'   => 'Personnel',
            'view'        => 'employees.php',
            'message'     => null,
            'messageType' => null,
        ];
    }

    private function notifications(): array
    {
        $model = new NotificationModel();

        $message = null;
        $messageType = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'update_status') {
                $id = (int) ($_POST['id'] ?? 0);
                $status = trim($_POST['status'] ?? '');

                if ($id <= 0 || $status === '') {
                    $message = 'Action invalide.';
                    $messageType = 'error';
                } else {
                    $ok = $model->updateInscriptionStatus($id, $status);

                    if ($ok) {
                        $message = 'Statut de l\'inscription mis à jour.';
                    } else {
                        $message = 'Erreur lors de la mise à jour.';
                        $messageType = 'error';
                    }
                }
            }

            if ($action === 'delete_inscription') {
                $id = (int) ($_POST['id'] ?? 0);

                if ($id <= 0) {
                    $message = 'ID invalide.';
                    $messageType = 'error';
                } else {
                    $ok = $model->deleteInscription($id);

                    if ($ok) {
                        $message = 'Inscription supprimée.';
                    } else {
                        $message = 'Erreur lors de la suppression.';
                        $messageType = 'error';
                    }
                }
            }
        }

        return [
            'currentPage' => 'notifications',
            'pageTitle'   => 'Notifications Système',
            'view'        => 'notifications.php',
            'message'     => $message,
            'messageType' => $messageType,
        ];
    }

    private function inscriptions(): array
    {
        $notificationModel = new NotificationModel();
        $orderModel = new OrderModel();
        $authModel = new AuthModel();

        $message = null;
        $messageType = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'update_inscription_status') {
                $id = (int) ($_POST['id'] ?? 0);
                $status = trim($_POST['status'] ?? '');

                if ($id <= 0 || $status === '') {
                    $message = 'Action invalide.';
                    $messageType = 'error';
                } else {
                    $ok = $notificationModel->updateInscriptionStatus($id, $status);

                    if ($ok) {
                        $message = 'Statut de l\'inscription mis à jour.';
                    } else {
                        $message = 'Erreur lors de la mise à jour.';
                        $messageType = 'error';
                    }
                }
            }

            if ($action === 'delete_inscription') {
                $id = (int) ($_POST['id'] ?? 0);

                if ($id <= 0) {
                    $message = 'ID invalide.';
                    $messageType = 'error';
                } else {
                    $ok = $notificationModel->deleteInscription($id);

                    if ($ok) {
                        $message = 'Inscription supprimée.';
                    } else {
                        $message = 'Erreur lors de la suppression.';
                        $messageType = 'error';
                    }
                }
            }
        }

        return [
            'currentPage' => 'inscriptions',
            'pageTitle'   => 'Inscriptions & Demandes',
            'view'        => 'inscriptions.php',
            'inscriptions' => $notificationModel->getAllInscriptions(),
            'quotes' => $orderModel->getAllQuotes(),
            'users' => $authModel->getAllUsers(),
            'message'     => $message,
            'messageType' => $messageType,
        ];
    }

    private function programmesImmobiliers(): array
    {
        $model = new ProgrammeImmobilierModel();

        $message = null;
        $messageType = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'add_programme') {
                $nom = trim($_POST['nom'] ?? '');
                $localisation = trim($_POST['localisation'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $surface_totale = (float) ($_POST['surface_totale'] ?? 0);
                $prix = (float) ($_POST['prix'] ?? 0);
                $nombre_unites = (int) ($_POST['nombre_unites'] ?? 0);
                $status = trim($_POST['status'] ?? 'planning');

                if ($nom === '' || $localisation === '' || $description === '') {
                    $message = 'Veuillez remplir tous les champs obligatoires.';
                    $messageType = 'error';
                } else {
                    $ok = $model->addProgramme(
                        $nom,
                        $localisation,
                        $description,
                        $surface_totale,
                        $prix,
                        $nombre_unites,
                        $status
                    );

                    if ($ok) {
                        $message = 'Programme immobilier ajouté avec succès.';
                    } else {
                        $message = 'Erreur lors de l\'ajout du programme.';
                        $messageType = 'error';
                    }
                }
            }

            if ($action === 'delete_programme') {
                $id = (int) ($_POST['id'] ?? 0);

                if ($id <= 0) {
                    $message = 'ID du programme invalide.';
                    $messageType = 'error';
                } else {
                    $ok = $model->deleteProgramme($id);

                    if ($ok) {
                        $message = 'Programme immobilier supprimé avec succès.';
                    } else {
                        $message = 'Erreur lors de la suppression du programme.';
                        $messageType = 'error';
                    }
                }
            }
        }

        return [
            'currentPage' => 'programmes-immobiliers',
            'pageTitle'   => 'Programmes immobiliers',
            'view'        => 'programmeImmobilier.php',
            'programmes'  => $model->getAllProgrammes(),
            'statistics'  => $model->getStatistics(),
            'message'     => $message,
            'messageType' => $messageType,
        ];
    }
}