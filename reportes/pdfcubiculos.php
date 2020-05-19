<?php
require('fpdf/fpdf.php');

class PDF extends FPDF
{
// Cabecera de página
function Header()
{
    // Logo
    $this->Image('img/Biblio_logo.png',9,8,33);
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Movernos a la derecha
    $this->Cell(70);
    // Título
    $this->Cell(60,10,'Reporte de Cubiculos',0,0,'C');
    // Salto de línea
    $this->Ln(40);
 $this->SetFont('Arial','B',12);
    $this->Cell(30,10,'Capacidad',1,0,'C',0);
    $this->Cell(40,10,'Limite de uso',1,0,'C',0);
    $this->Cell(60,10,utf8_decode('Ubicación'),1,0,'C',0);
    $this->Cell(60,10,'Estado',1,1,'C',0);
}

// Pie de página
function Footer()
{
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Número de página
    $this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
}
}

// Creación del objeto de la clase heredada
include "../funciones.php";
$con = conectar();
$consulta = "select elementos.capacidad,elementos.descripcion,elementos.limiteDeUso,elementos.ubicacion,estados.descripcion from elementos, estados where elementos.estadoID = estados.estadoID";
$resultado = mysqli_query($con, $consulta);
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',10);

while($row=$resultado->fetch_assoc()){
    $pdf->Cell(30,10,$row['capacidad'],1,0,'C',0);
    $pdf->Cell(40,10,$row['limiteDeUso'],1,0,'C',0);
    $pdf->Cell(60,10,$row['ubicacion'],1,0,'C',0);
    $pdf->Cell(60,10,$row['descripcion'],1,1,'C',0);
}

$pdf->Output();
?>
