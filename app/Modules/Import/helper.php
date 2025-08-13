<?php

function CheckExcelHeader($excelHeader='',$fixheader='') {
	  
	  // dd($excelHeader,$fixheader);
	  $result=array_diff_assoc($excelHeader,$fixheader);
	  if(empty($result))
	  return true;
	  else 
	  return false;
} 