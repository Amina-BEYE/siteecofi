<?php

declare(strict_types=1);


require_once __DIR__ . '/../Core/Database.php';

use App\Core\Database;


// FPDF s'installe via composer : composer require setasign/fpdf
require_once __DIR__ . '/../../vendor/setasign/fpdf/fpdf.php';

/**
 * Étend FPDF pour ajouter l'en-tête, le pied de page et les helpers métier.
 */
class DevisPDF extends FPDF
{
    private array $devis;
    private array $lignes;

    // Palette de couleurs
    private const NAVY   = [26,  58, 107];  // #1a3a6b
    private const GRAY_L = [248, 250, 252];  // #F8FAFC
    private const GRAY_D = [100, 116, 139];  // #64748b
    private const WHITE  = [255, 255, 255];
    private const RED    = [220,  38,  38];

    public function __construct(array $devis, array $lignes)
    {
        parent::__construct('P', 'mm', 'A4');
        $this->devis  = $devis;
        $this->lignes = $lignes;

        $this->SetMargins(18, 18, 18);
        $this->SetAutoPageBreak(true, 20);
        $this->AddPage();
        $this->SetFont('Helvetica', '', 10);
    }

    // ------------------------------------------------------------------ Header
    public function Header(): void
    {
        // Bande de couleur gauche (logo simulé)
        $this->SetFillColor(...self::NAVY);
        $this->Rect(0, 0, 8, 297, 'F');

        // Nom de la société
        $this->SetXY(14, 14);
        $this->SetFont('Helvetica', 'B', 18);
        $this->SetTextColor(...self::NAVY);
        $this->Cell(80, 8, 'ECOFI', 0, 0, 'L');

        // Tagline
        $this->SetXY(14, 23);
        $this->SetFont('Helvetica', '', 8);
        $this->SetTextColor(...self::GRAY_D);
        $this->Cell(80, 5, 'Solutions financieres & industrielles', 0, 1, 'L');

        // Coordonnées société
        $this->SetFont('Helvetica', '', 8);
        $this->SetTextColor(75, 85, 99);
        foreach ([
            '12 Rue du Commerce, Dakar — Senegal',
            'Tel : +221 33 820 00 00',
            'service.ecofi01@gmail.com',
            'NINEA : 00123456 7A2',
        ] as $line) {
            $this->SetX(14);
            $this->Cell(80, 4.5, $line, 0, 1, 'L');
        }

        // Bloc DEVIS (droite)
        $this->SetFont('Helvetica', 'B', 20);
        $this->SetTextColor(...self::NAVY);
        $this->SetXY(120, 14);
        $this->Cell(72, 10, 'DEVIS', 0, 0, 'R');

        $this->SetFont('Helvetica', 'B', 11);
        $this->SetXY(120, 25);
        $this->Cell(72, 6, 'N° ' . $this->devis['numero_devis'], 0, 1, 'R');

        $this->SetFont('Helvetica', '', 8);
        $this->SetTextColor(...self::GRAY_D);
        $this->SetX(120);
        $this->Cell(72, 4.5, 'Emis le : ' . $this->devis['date_emission'], 0, 1, 'R');
        $this->SetX(120);
        $this->Cell(72, 4.5, 'Valide jusqu au : ' . $this->devis['date_validite'], 0, 1, 'R');

        // Ligne de séparation
        $this->SetDrawColor(...self::NAVY);
        $this->SetLineWidth(0.6);
        $this->Line(14, 52, 196, 52);
        $this->SetLineWidth(0.2);

        $this->SetY(56);
    }

    // ------------------------------------------------------------------ Footer
    public function Footer(): void
    {
        $this->SetY(-14);
        $this->SetDrawColor(...self::NAVY);
        $this->SetLineWidth(0.4);
        $this->Line(14, $this->GetY(), 196, $this->GetY());

        $this->SetFont('Helvetica', 'I', 7);
        $this->SetTextColor(...self::GRAY_D);
        $this->SetX(14);
        $this->Cell(0, 6,
            'ECOFI SARL — RC : SN DKR 2018 B 12345 — Document confidentiel — Page '
            . $this->PageNo() . '/{nb}',
            0, 0, 'C'
        );
    }

    // ----------------------------------------------------------- Bloc émetteur / client
    public function addParties(): void
    {
        $y = $this->GetY();

        // Bloc ÉMETTEUR
        $this->setFillGray();
        $this->RoundedRect(14, $y, 83, 32, 2, 'F');
        $this->addPartyLabel(14, $y + 3, 'EMETTEUR');
        $this->addPartyContent(14, $y + 8, [
            ['B', 'ECOFI SARL'],
            ['',  '12 Rue du Commerce, Dakar'],
            ['',  'service.ecofi01@gmail.com'],
            ['',  'RC : SN DKR 2018 B 12345'],
        ]);

        // Bloc CLIENT
        $this->setFillGray();
        $this->RoundedRect(107, $y, 89, 32, 2, 'F');
        $this->addPartyLabel(107, $y + 3, 'DESTINATAIRE');
        $this->addPartyContent(107, $y + 8, [
            ['B', $this->devis['client_nom']],
            ['',  $this->devis['client_contact'] ?? ''],
            ['',  $this->devis['client_email']],
            ['',  $this->devis['client_telephone'] ?? ''],
        ]);

        $this->SetY($y + 38);
    }

    // ----------------------------------------------------------- Tableau des lignes
    public function addTable(): void
    {
        // En-tête du tableau
        $this->SetFillColor(...self::NAVY);
        $this->SetTextColor(...self::WHITE);
        $this->SetFont('Helvetica', 'B', 8);
        $this->SetDrawColor(...self::NAVY);

        $cols = [
            ['Ref.',        14,  'L'],
            ['Designation', 68,  'L'],
            ['Qte',         14,  'C'],
            ['P.U. HT',     32,  'R'],
            ['Total HT',    34,  'R'],
        ];

        foreach ($cols as [$label, $w, $align]) {
            $this->Cell($w, 8, $label, 0, 0, $align, true);
        }
        $this->Ln();

        // Lignes de détail
        $fill = false;
        $totalHT = 0.0;

        foreach ($this->lignes as $ligne) {
            $lineTotal = $ligne['quantite'] * $ligne['prix_unitaire'];
            $totalHT  += $lineTotal;

            if ($fill) {
                $this->SetFillColor(...self::GRAY_L);
            } else {
                $this->SetFillColor(...self::WHITE);
            }

            $this->SetTextColor(55, 65, 81);
            $startY = $this->GetY();

            // Référence
            $this->SetFont('Helvetica', '', 7.5);
            $this->SetTextColor(...self::GRAY_D);
            $this->SetX(14);
            $this->Cell(14, 12, $ligne['reference'] ?? '', 0, 0, 'L', $fill);

            // Désignation (2 lignes : titre + description)
            $this->SetFont('Helvetica', 'B', 8.5);
            $this->SetTextColor(...self::NAVY);
            $this->SetXY(28, $startY + 1.5);
            $this->Cell(68, 5, $ligne['designation'], 0, 0, 'L');

            $this->SetFont('Helvetica', '', 7.5);
            $this->SetTextColor(...self::GRAY_D);
            $this->SetXY(28, $startY + 6.5);
            $this->Cell(68, 4.5, $ligne['description'] ?? '', 0, 0, 'L');

            // Qté, PU, Total
            $this->SetFont('Helvetica', '', 9);
            $this->SetTextColor(55, 65, 81);

            $this->SetXY(96, $startY);
            $this->Cell(14, 12, (string)$ligne['quantite'], 0, 0, 'C', $fill);

            $this->SetXY(110, $startY);
            $this->Cell(32, 12, number_format($ligne['prix_unitaire'], 0, ',', ' '), 0, 0, 'R', $fill);

            $this->SetXY(142, $startY);
            $this->SetFont('Helvetica', 'B', 9);
            $this->Cell(54, 12, number_format($lineTotal, 0, ',', ' '), 0, 0, 'R', $fill);

            $this->SetY($startY + 12);
            $fill = !$fill;
        }

        $this->SetY($this->GetY() + 4);
        $this->addTotals($totalHT);
    }

    // ----------------------------------------------------------- Bloc totaux
    private function addTotals(float $totalHT): void
    {
        $remise    = $totalHT * ($this->devis['remise_pct'] / 100);
        $baseImpo  = $totalHT - $remise;
        $tva       = $baseImpo * ($this->devis['tva_pct'] / 100);
        $totalTTC  = $baseImpo + $tva;

        $x = 110;
        $w1 = 50;
        $w2 = 36;

        $rows = [
            ['Sous-total HT',                                        $totalHT,  false, false],
            ['Remise (' . $this->devis['remise_pct'] . '%)',          -$remise,  true,  false],
            ['Base imposable',                                        $baseImpo, false, false],
            ['TVA (' . $this->devis['tva_pct'] . '%)',                $tva,      false, false],
            ['Total TTC',                                             $totalTTC, false, true],
        ];

        foreach ($rows as [$label, $montant, $isRemise, $isFinal]) {
            $y = $this->GetY();

            if ($isFinal) {
                $this->SetFillColor(...self::NAVY);
                $this->Rect($x, $y, $w1 + $w2, 10, 'F');
                $this->SetTextColor(...self::WHITE);
                $this->SetFont('Helvetica', 'B', 10);
                $this->SetXY($x, $y + 1);
                $this->Cell($w1, 8, $label, 0, 0, 'L');
                $this->Cell($w2, 8, number_format($montant, 0, ',', ' ') . ' FCFA', 0, 1, 'R');
            } else {
                $this->SetFont($isRemise ? 'Helvetica' : 'Helvetica', '', 9);
                $this->SetTextColor($isRemise ? 220 : 75, $isRemise ? 38 : 85, $isRemise ? 38 : 99);
                $this->SetXY($x, $y);
                $this->Cell($w1, 7, $label, 0, 0, 'L');
                $prefix = $isRemise ? '- ' : '';
                $this->Cell($w2, 7,
                    $prefix . number_format(abs($montant), 0, ',', ' ') . ' FCFA',
                    0, 1, 'R'
                );
            }
        }
    }

    // ----------------------------------------------------------- Conditions & signature
    public function addFooterZone(): void
    {
        $this->SetY($this->GetY() + 8);
        $y = $this->GetY();

        // Conditions de règlement
        $this->setFillGray();
        $this->RoundedRect(14, $y, 95, 36, 2, 'F');

        $this->SetFont('Helvetica', 'B', 7.5);
        $this->SetTextColor(...self::NAVY);
        $this->SetXY(18, $y + 3);
        $this->Cell(87, 5, 'CONDITIONS DE REGLEMENT', 0, 1, 'L');

        $this->SetFont('Helvetica', '', 8);
        $this->SetTextColor(75, 85, 99);
        foreach ([
            '30% a la commande — 70% a la livraison',
            'Virement bancaire — Delai : 30 jours nets',
            '',
            $this->devis['notes'] ?? 'Garantie materiel : 12 mois pieces & MO.',
        ] as $note) {
            $this->SetX(18);
            $this->Cell(87, 4.5, $note, 0, 1, 'L');
        }

        // Zone de signature
        $this->SetDrawColor(203, 213, 225);
        $this->SetLineWidth(0.3);
        $this->Rect(117, $y, 79, 36);

        $this->SetFont('Helvetica', 'B', 7.5);
        $this->SetTextColor(...self::NAVY);
        $this->SetXY(117, $y + 3);
        $this->Cell(79, 5, 'BON POUR ACCORD — CACHET & SIGNATURE', 0, 1, 'C');

        $this->SetDrawColor(203, 213, 225);
        $this->Line(122, $y + 30, 191, $y + 30);

        $this->SetFont('Helvetica', '', 7.5);
        $this->SetTextColor(...self::GRAY_D);
        $this->SetXY(117, $y + 31);
        $this->Cell(79, 4, 'Date : _____ / _____ / _________', 0, 0, 'C');
    }

    // ----------------------------------------------------------- Helpers
    private function addPartyLabel(float $x, float $y, string $label): void
    {
        $this->SetFont('Helvetica', 'B', 7);
        $this->SetTextColor(...self::GRAY_D);
        $this->SetXY($x + 4, $y);
        $this->Cell(75, 4, $label, 0, 1, 'L');
    }

    private function addPartyContent(float $x, float $y, array $lines): void
    {
        foreach ($lines as [$style, $text]) {
            if ($text === '') continue;
            $this->SetFont('Helvetica', $style, $style === 'B' ? 9.5 : 8);
            $this->SetTextColor($style === 'B' ? 26 : 75, $style === 'B' ? 58 : 85, $style === 'B' ? 107 : 99);
            $this->SetXY($x + 4, $y);
            $this->Cell(80, 4.5, $text, 0, 1, 'L');
            $y += 4.5;
        }
    }

    private function setFillGray(): void
    {
        $this->SetFillColor(...self::GRAY_L);
        $this->SetDrawColor(226, 232, 240);
    }

    /**
     * Rectangle avec coins arrondis (non natif dans FPDF de base).
     */
    public function RoundedRect(
        float $x, float $y, float $w, float $h,
        float $r, string $style = ''
    ): void {
        $k  = $this->k;
        $hp = $this->h;
        $op = ($style === 'F') ? 'f' : (($style === 'FD' || $style === 'DF') ? 'b' : 's');
        $myArc = 4 / 3 * (M_SQRT2 - 1);
        $this->_out(sprintf('q %.2F %.2F m',
            ($x + $r) * $k, ($hp - $y) * $k));
        $xc = $x + $w - $r; $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - $y) * $k));
        $this->_Arc($xc + $r * $myArc, $yc - $r, $xc + $r, $yc - $r * $myArc, $xc + $r, $yc);
        $xc = $x + $w - $r; $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $yc) * $k));
        $this->_Arc($xc + $r, $yc + $r * $myArc, $xc + $r * $myArc, $yc + $r, $xc, $yc + $r);
        $xc = $x + $r; $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - ($y + $h)) * $k));
        $this->_Arc($xc - $r * $myArc, $yc + $r, $xc - $r, $yc + $r * $myArc, $xc - $r, $yc);
        $xc = $x + $r; $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $x * $k, ($hp - $yc) * $k));
        $this->_Arc($xc - $r, $yc - $r * $myArc, $xc - $r * $myArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    private function _Arc(
        float $x1, float $y1, float $x2, float $y2,
        float $x3, float $y3
    ): void {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
            $x1 * $this->k, ($h - $y1) * $this->k,
            $x2 * $this->k, ($h - $y2) * $this->k,
            $x3 * $this->k, ($h - $y3) * $this->k
        ));
    }
}