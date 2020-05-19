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
    $this->Cell(60,10,'Reporte de Registros',0,0,'C');
    // Salto de línea
    $this->Ln(40);
 $this->SetFont('Arial','B',12);
    $this->Cell(40,10,'Carnet',1,0,'C',0);
    $this->Cell(30,10,'Fecha',1,0,'C',0);
    $this->Cell(20,10,'Entrada',1,0,'C',0);
    $this->Cell(20,10,'Salida',1,0,'C',0);
    $this->Cell(40,10,'Autorizacion',1,0,'C',0);
    $this->Cell(40,10,utf8_decode('Descripción'),1,1,'C',0);
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
$consulta = "select registros.carnetUsuario, registros.fecha, registros.entrada, registros.salida, registros.autorizacion, elementos.descripcion from registros, elementos where registros.elementoID = elementos.elementoID";
$resultado = mysqli_query($con, $consulta);
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',10);

while($row=$resultado->fetch_assoc()){
    $pdf->Cell(40,10,$row['carnetUsuario'],1,0,'C',0);
    $pdf->Cell(30,10,$row['fecha'],1,0,'C',0);
    $pdf->Cell(20,10,$row['entrada'],1,0,'C',0);
    $pdf->Cell(20,10,$row['salida'],1,1,'C',0);
    $pdf->Cell(40,10,$row['autorizacion'],1,0,'C',0);
    $pdf->Cell(40,10,$row['descripcion'],1,0,'C',0);
}

$pdf->Output();
?>