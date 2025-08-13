<?php


	$_SESSION['carttotal'] = $total;
	$netamt=$total;

	# Fetch value from POST or GET Fields
	if (isset($_SESSION['coupon_code']))
	{
		if($_SESSION['coupon_code'] != "")
		{
			//echo "S";exit;
			function CheckCode($res)
			{
				$CustomerID			= $_SESSION["CustomerID"];
				$cnt_session_cart 	= count($_SESSION['cartitems']);
				$discount = 0;
				$tst_cnt = 0;
				global $objDB;
		
				if ($CustomerID > 0)
				{
					# Coupon For Only One Usage
					if ($res[0]['ExpiresonFirstUseByAnyCustomer'] == 1) {
						$sql_usage = "SELECT * FROM couponusage WHERE CouponCode = '".$res[0]['CouponsCode']."' AND StoreID = ".$_SESSION["StoreID"];
						$res_usage = $objDB->sql_query($sql_usage);
					
						if (count($res_usage) > 0)
						{
							$returnArr['msg'] = "Sorry, Promo Code Expired.";
							$returnArr['err'] = 1;
							return $returnArr;
						} else {
							$tst_cnt = 1;
						}
					} else if ($res[0]['ExpiresAfterOneUsageByEachCustomer'] == 1) {
						$sql_usage = "SELECT * FROM couponusage WHERE CouponCode = '".$res[0]['CouponsCode']."'  AND StoreID = ".$_SESSION["StoreID"]." AND CustomerID = '".$CustomerID."'";
						$res_usage = $objDB->sql_query($sql_usage);

						if (count($res_usage) > 0)
						{
							$returnArr['msg'] = "Sorry, This Promo code already used by you.";
							$returnArr['err'] = 1;
							return $returnArr;
						} else {
							$tst_cnt = 1;
						}
					} else if ($res[0]['ExpiredAfterNUses'] == 1) {
						$sql_usage = "SELECT * FROM couponusage WHERE CouponCode = '".$res[0]['CouponsCode']."' AND StoreID = ".$_SESSION["StoreID"];
						$res_usage = $objDB->sql_query($sql_usage);				
						$cnt_usage = count($res_usage);
						
						if ($cnt_usage >= $res[0]['NumUses']) {
							$returnArr['msg'] = "Sorry, This Promo Code is expired.";
							$returnArr['err'] = 1;
							return $returnArr;
						} else {
							$tst_cnt = 1;	
						}
					}
				}

				if (($res[0]['Valid_Customers'] == 'allcustomers' || strlen($res[0]['Valid_Customers']) > 0) && $tst_cnt == 1)
				{
					if ($res[0]['Valid_Customers'] == 'allcustomers') {
						$discount = CalculateDiscount($res);
						if ($discount == -1) {
							$returnArr['msg'] = "Sorry, Promo Code not valid for this product.";
							$returnArr['err'] = 1;	
						} else {
							$returnArr['msg'] = $discount;
							$returnArr['err'] = 0;	
						}
						return $returnArr;
					} else {
						$customer_list = explode(",", $res[0]['Valid_Customers']);				
						//if (in_array($CustomerID,$customer_list) || $res[0]['Valid_Customers'] > 0)
						if (in_array($CustomerID,$customer_list))
						{
							$discount = CalculateDiscount($res);
							if ($discount == -1) {
								$returnArr['msg'] = "Sorry, Promo Code not valid for this product.";
								$returnArr['err'] = 1;	
							} else {
								$returnArr['msg'] = $discount;
								$returnArr['err'] = 0;	
							}
							return $returnArr;
						} else {
							$returnArr['msg'] = "Sorry, Promo Code is not available for you.";
							$returnArr['err'] = 1;
							return $returnArr;
						}
					}			
				} elseif ($res[0]['Valid_Customers'] == 'allcustomers') {
					$discount = CalculateDiscount($res);			
					if ($discount == -1) {
						$returnArr['msg'] = "Sorry, Promo Code not valid for this product.";
						$returnArr['err'] = 1;	
					} else {
						$returnArr['msg'] = $discount;
						$returnArr['err'] = 0;	
					}
					return $returnArr;
				} else {
					$returnArr['msg'] = "Sorry, Promo Code is available only for registered customer.";
					$returnArr['err'] = 1;
					return $returnArr;
				}
			}
	
			function CalculateDiscount($res)
			{
				global $objDB;
				$cnt_session_cart = count($_SESSION['cartitems']);
				$valid_cat 	= $res[0]['Valid_Categories'];
				$valid_prod = $res[0]['Valid_Products'];
				$discount = 0;
				/*
					Fetch all products of particular manufacturer if coupon code is set for manufacturer
				*/
				$ForManufacture = $res[0]['ForManufacture'];
				//if($ForManufacture != '' && $ForManufacture != 'Select Manufacture' && $ForManufacture != 'allmanufacture')
				if($ForManufacture != '' && $ForManufacture != 'Select Manufacture'  && $ForManufacture != 'allmanufacture')
				{
                                        $SQL = "SELECT MasterId FROM store_".$_SESSION['StoreID']."_master WHERE Manufacturer IN (".$ForManufacture.")";
                                        $RS = $objDB->select($SQL);
                                        $tmp = '';
                                        for($i=0; $i < count($RS); $i++)
                                        {
                                                $tmp .=",".$RS[$i]['MasterId'];
                                        }
                                        $tmp = trim($tmp, ",");
					$valid_prod = $tmp;
				}
				/*
					Fetch all products which are marked as  sale/discontinued/item-on-sale if coupon code is set for sale products
				*/
				$OnlyForSaleProduct = $res[0]['OnlyForSaleProduct'];
				if($OnlyForSaleProduct == 'Y')
				{
					$SQL = "SELECT MasterId FROM store_".$_SESSION['StoreID']."_master WHERE (Discontinued='Yes' OR NewItemOnSale='Yes' OR Sale='Yes') AND Active='Yes' AND Deleted='0'";
					$RS = $objDB->select($SQL);
					$tmp = '';
					for($i=0; $i < count($RS); $i++)
					{
						$tmp .=",".$RS[$i]['MasterId'];
					}
					$tmp = trim($tmp, ",");
					$valid_prod = $tmp;
				}
				
				# Fetch All Product Ids
				if ($valid_prod == 'allproducts')
				{

					if ($valid_cat != 'allcategories')
					{
						if(strpos($valid_cat, ",") !== false) {
							$sql_cat = "SELECT tid FROM term_hierarchy WHERE parent IN (".$valid_cat.") AND StoreID=".$_SESSION['StoreID'];
							$res_cat = $objDB->select($sql_cat);
							
							$AllProdIDs = "";
							for($ct=0; $ct < count($res_cat); $ct++)
							{
								$AllProdIDs .= $res_cat[$ct]['tid'].",";
							}
							$AllProdIDs = trim($AllProdIDs, ",");
//							$AllProdIDs = $res_cat[0]['AllMasterIDs'];

						} else {
							$sql_cat = "SELECT tid FROM term_hierarchy WHERE parent = ".$valid_cat." AND StoreID=".$_SESSION['StoreID'];
							$res_cat = $objDB->select($sql_cat);
														$AllProdIDs = "";
							for($ct=0; $ct < count($res_cat); $ct++)
							{
								$AllProdIDs .= $res_cat[$ct]['tid'].",";
							}
							$AllProdIDs = trim($AllProdIDs, ",");
						
//							$AllProdIDs = $res_cat[0]['AllMasterIDs'];
						}
					} else {
						$AllProdIDs = 'all';
					}
				} elseif (strpos($valid_prod, ",") !== false) {
					$AllProdIDs = $valid_prod;
					if ($valid_cat != 'allcategories')
					{
						if(strpos($valid_cat, ",") !== false) {
							$sql_cat = "SELECT tid FROM term_hierarchy WHERE parent IN (".$valid_cat.") AND StoreID = ".$_SESSION['StoreID'];
							$res_cat = $objDB->select($sql_cat);
							$AllProdIDs = "";
							for($ct=0; $ct < count($res_cat); $ct++)
							{
								$AllProdIDs .= $res_cat[$ct]['tid'].",";
							}
							$AllProdIDs = trim($AllProdIDs, ",");
							//$AllProdIDs = $res_cat[0]['AllMasterIDs'];
						} else {
							$sql_cat = "SELECT tid FROM term_hierarchy WHERE parent = ".$valid_cat." AND tid IN (".$AllProdIDs.") AND StoreID = ".$_SESSION['StoreID'];
							$res_cat = $objDB->select($sql_cat);
							$AllProdIDs = "";
							for($ct=0; $ct < count($res_cat); $ct++)
							{
								$AllProdIDs .= $res_cat[$ct]['tid'].",";
							}
							$AllProdIDs = trim($AllProdIDs, ",");
							//$AllProdIDs = $res_cat[0]['AllMasterIDs'];
						}
					} else {
						$AllProdIDs = $valid_prod;
					}
				} elseif (strlen($valid_prod) > 0) {
					$AllProdIDs = $valid_prod;
					if ($valid_cat != 'allcategories')
					{
						if(strpos($valid_cat, ",") !== false) {
							$sql_cat = "SELECT tid FROM term_hierarchy WHERE parent IN (".$valid_cat.") AND StoreID = ".$_SESSION['StoreID'];
							$res_cat = $objDB->select($sql_cat);
							$AllProdIDs = "";
							for($ct=0; $ct < count($res_cat); $ct++)
							{
								$AllProdIDs .= $res_cat[$ct]['tid'].",";
							}
							$AllProdIDs = trim($AllProdIDs, ",");
							//$AllProdIDs = $res_cat[0]['AllMasterIDs'];
						} else {
							$sql_cat = "SELECT tid FROM term_hierarchy WHERE parent = ".$valid_cat." AND tid = ".$AllProdIDs." AND StoreID=".$_SESSION['StoreID'];
							$res_cat = $objDB->select($sql_cat);
							$AllProdIDs = "";
							for($ct=0; $ct < count($res_cat); $ct++)
							{
								$AllProdIDs .= $res_cat[$ct]['tid'].",";
							}
							$AllProdIDs = trim($AllProdIDs, ",");
						//	$AllProdIDs = $res_cat[0]['AllMasterIDs'];
						}
					} else {
						$AllProdIDs = $valid_prod;
					}
				} else {
				}

				if ($AllProdIDs == "all")
				{
					$all_ids = 'all';
				} else {
					if (count($AllProdIDs) > 0) {
						$all_ids = $AllProdIDs;
						$all_ids = explode(",",$all_ids);
					} else {
						$discount = -1;
						return $discount;	
					}
				}
				
				$discount = FindCategoryProduct($res,$all_ids);		
				return $discount;
			}//end CalculateDiscount
	
			function FindCategoryProduct($res,$prod_ids)
			{
			
				$cnt_session_cart 	= count($_SESSION['cartitems']);
				$tmp_cnt = 0;
				$discount = 0;
				if ($prod_ids != "all") {
					for ($k=0; $k<$cnt_session_cart; $k++)
					{
						if (in_array($_SESSION['cartitems'][$k]['MasterId'], $prod_ids))
						{
							if ($res[0]['DiscountPercent'] > 0)
							{
								$discount += ($_SESSION['cartitems'][$k]['ProductSalePrice'] * $_SESSION['cartitems'][$k]['ProductQty'] * $res[0]['DiscountPercent']) / 100;
								$_SESSION['discount_type']  = "percentage";
							} else {
								$discount += $res[0]['DiscountAmount'];
								$_SESSION['discount_type']  = "amount";
							}
						} else {
							$tmp_cnt++;	
						}
					}//end i
					if ($tmp_cnt == $cnt_session_cart) {
						return -1;
					} else {
						return $discount;
					}
				}//end if
				else {
				
					if ($res[0]['DiscountPercent'] > 0)
					{
						$discount = ($_SESSION['carttotal'] * $res[0]['DiscountPercent']) / 100;
						$_SESSION['discount_ms'] = $res[0]['DiscountPercent'];
						$_SESSION['discount_type']  = "percentage";
					} else {
						$discount = $res[0]['DiscountAmount'];
						$_SESSION['discount_ms'] = $res[0]['DiscountAmount'];
						$_SESSION['discount_type']  = "amount";
					}
				}
				return $discount;
			}//end FindCategoryProduct
	  
			$CustomerID		= $_SESSION["CustomerID"];
			$p 				= loadVariable("p",'shoppingcart');
			$msg 			= loadVariable("msg",'');
			$PromoCode		= $_SESSION['coupon_code'];
			$PromoCode		= trim($PromoCode);
			$CartTotal		= $_SESSION['carttotal'];	
			$StoreID		= $_SESSION['StoreID'];
			$amt_require    = loadVariable("amt_require","");
			$session_cc		= $_SESSION['coupon_code'];

			if(isset($_POST['promo_code']))
			{
				$sql = "SELECT * FROM coupons WHERE CouponType = 'Coupon' AND CouponsCode = '".$_POST['promo_code']."'";
				if($_SESSION['AccountType']!='')
					$sql.=" AND CouponFor='".$_SESSION['AccountType']."'";
				$sql.="  AND Deleted = 0 AND StoreID = ".$StoreID;   

				$res = $objDB->select($sql);
			}
			else
			{
				$sql = "SELECT * FROM coupons WHERE CouponType = 'Coupon' AND CouponsCode = '".$_SESSION['coupon_code']."'";
				if($_SESSION['AccountType']!='')
					$sql.=" AND CouponFor='".$_SESSION['AccountType']."'";
				$sql.="  AND Deleted = 0 AND StoreID = ".$StoreID;   
				$res = $objDB->select($sql);
			}
		/*	$sql = "SELECT * FROM coupons WHERE CouponType = 'Coupon' AND CouponsCode = '".$PromoCode."'";
            if($_SESSION['AccountType']!='')
                $sql.=" AND CouponFor='".$_SESSION['AccountType']."'";
            $sql.="  AND Deleted = 0 AND StoreID = ".$StoreID;            
            $res = $objDB->select($sql);*/
            
			if (count($res) > 0)
			{
				$current_time = date("Y-m-d H:i:s", time());
				if ($res[0]['ExpirationDate'] > $current_time)
				{
					
					if ($res[0]['RequiredMinimumOrderTotal'] >= 0.0000)
					{
						
						if ($res[0]['RequiredMinimumOrderTotal'] <= $_SESSION['carttotal'])
						{
							
							$returnArr = CheckCode($res);
							if ($returnArr['err'] == 0)
							{
								$_SESSION['coupon_code'] = $PromoCode;								
									$discount = number_format($returnArr['msg'],2);
								$netamt = $_SESSION['carttotal'] - number_format($discount,2);
								if($netamt < 0){
									$_SESSION['coupon_code'] = $session_cc;
									echo "<label class='promo_error' style='color:#f00'>Your cart total should be greater than 0 after applying promocode.</label>";
								} else {
									$_SESSION['discount'] =  $discount;
									
									?>
									<script type="text/javascript" language="javascript1.1">
										var discount = Number(<?=$discount?>);
										var netamt = Number(<?=$netamt?>);
										if($('#OrderTax').length > 0){
											var orderpromotax = Number($('#OrderTax').val());
										}else{
											var orderpromotax = 0;
										}
										
										var cust_level_discount = document.getElementById('cust_level_discount').value;
										if (cust_level_discount != "" && cust_level_discount > 0) {
											netamt = netamt - cust_level_discount;
										}
										var shipcharge = document.getElementById('shipcharge').value;
										if (shipcharge != "" || shipcharge > 0) {
											netamt = netamt + Number(shipcharge) + orderpromotax;
										} else {
											shipcharge = 0;
										}										
										document.getElementById('NetDiscount').innerHTML= discount.toFixed(2);

										document.getElementById('discount').value 	 	= discount.toFixed(2);
										document.getElementById('NetTotal').innerHTML 	= netamt.toFixed(2);
                                        <? if($amt_require!='') { ?>
                                                $('#ReqNetTotal').html(number_format(((netamt*<?=$amt_require?>)/100),2));
                                        <? } ?>
                                        document.getElementById('maintotal').value 		= netamt.toFixed(2);
									</script>
								<?php
									if(isset($_POST['promo_code']))
									{
										$_SESSION['coupon_code']=$_POST['promo_code'];	
										echo "<label class='promo_error' style='color:#8DBF3B'>Promo Code applied successfully.</label>";
									}
								}
							} else {
								echo "<label class='promo_error' style='color:#f00'>".$returnArr['msg']."</label>";
									$_SESSION['coupon_code'] = '';
									unset($_SESSION['discount']);
									unset($_SESSION['coupon_code']);
								
							}
						} else {
							echo "<label class='promo_error' style='color:#f00'>Sorry, Promo Code could not be applied. Your CartTotal must be greater than $".number_format($res[0]['RequiredMinimumOrderTotal'],2)." for Promo Code to be applicable.</label>";
							
									$_SESSION['coupon_code'] = '';
									unset($_SESSION['discount']);									
									unset($_SESSION['coupon_code']);
								
						}					
					} else {						
						$returnArr = CheckCode($res);						
						if ($returnArr['err'] == 0)
						{
							$_SESSION['coupon_code'] = $PromoCode;
							$discount = number_format($returnArr['msg'],2);
							$netamt = $_SESSION['carttotal'] - number_format($discount,2);
							
							if($netamt < 0){
								$_SESSION['coupon_code'] = $session_cc;
								echo "<label class='promo_error'>Your cart total should be greater than promocode amount.</label>";
							} else {
								$_SESSION['discount'] =  $discount;
								?>
								<script type="text/javascript" language="javascript1.1">
									var discount = Number(<?=$discount?>);	
									var netamt = Number(<?=$netamt?>);
									if($('#OrderTax').length > 0){
										var orderpromotax = Number($('#OrderTax').val());
									}else{
										var orderpromotax = 0;
									}
									var shipcharge = document.getElementById('shipcharge').value;
									if (shipcharge != "" || shipcharge > 0) {
										netamt = netamt + Number(shipcharge) + orderpromotax;
									} else {
										shipcharge = 0;
									}

									if('<?=$p?>'!='shoppingcart')
									{
										var cust_level_discount = document.getElementById('cust_level_discount').value;
										if (cust_level_discount != "" && cust_level_discount > 0) {
											netamt = netamt - cust_level_discount;
										}
									}
									if (document.getElementById('NetDiscount')) {
										document.getElementById('NetDiscount').innerHTML= discount.toFixed(2);
									}
									document.getElementById('discount').value 	 	= discount.toFixed(2);
									if (document.getElementById('NetTotal')) {
										document.getElementById('NetTotal').innerHTML 	= netamt.toFixed(2);
									}
									<?
									if($amt_require!='')
									{
										?>
											$('#ReqNetTotal').html(number_format(((netamt*<?=$amt_require?>)/100),2));
										<?
									}
									?>
									document.getElementById('maintotal').value 		= netamt.toFixed(2);
								</script>
								<?php
								if(isset($_POST['promo_code']))
									{
										$_SESSION['coupon_code']=$_POST['promo_code'];	
										echo "<label class='promo_error' style='color:#8DBF3B'>Promo Code applied successfully.</label>";
									}
							}
						} else {
							echo "<label class='promo_error' style='color:#f00'>".$returnArr['msg']."</label>";
							$_SESSION['coupon_code'] = '';
							unset($_SESSION['coupon_code']);
							unset($_SESSION['discount']);
						}
					}
				}else {
					echo "<label class='promo_error' style='color:#f00'>This Promo code is expired.</label>";
						$_SESSION['coupon_code'] = '';
						unset($_SESSION['discount']);					
						unset($_SESSION['coupon_code']);
				}
			}
			else {
				echo "<label class='promo_error' style='color:#f00'>Invalid Promo Code.</label>";

					unset($_SESSION['discount']);					
					unset($_SESSION['coupon_code']);
			}
		}
	}	
	else {
		unset($_SESSION['discount']);
		$netamt = $_SESSION['carttotal'] - number_format(($_SESSION['discount']+$_SESSION['groupdiscount']),2);
	}

	if(!isset($_SESSION['coupon_code']))
	{
		if(check_free_promotion_product())
		{
			remove_free_promotion_product();
			header("Location: ".SERVER_ROOT."shoppingcart.html?cpn=err");
			exit;
		}
	}
	else
	{
		if(strtoupper($_SESSION['coupon_code']) == "LOVEMOM" && !check_free_promotion_product())
		{
			$FreeSKUs = explode(",", FREE_MOTHER_PROMOTION);

			for($i=0; $i < count($FreeSKUs); $i++)
			{
				$rsInfo = $prodObj->fetch_product_info_by_sku($FreeSKUs[$i]); 	
				if(count($rsInfo) > 0)
				{
					$InventoryS = $prodObj->fetch_session_product_qty($rsInfo[0]['MasterId']); 	
					if($InventoryS+1 <= $rsInfo[0]['Inventory'])
					{
						$pcnt=count($_SESSION['cartitems']);
						$_SESSION['cartitems'][$pcnt]['FreeType'] = "PromotionFree";
						$_SESSION['cartitems'][$pcnt]['MasterId'] = $rsInfo[0]['MasterId'];
						$_SESSION['cartitems'][$pcnt]['ProductImage'] = $rsInfo[0]['ProductImage'];
						$_SESSION['cartitems'][$pcnt]['ProductName'] = $rsInfo[0]['Name'];
						$_SESSION['cartitems'][$pcnt]['ProductSKU'] = $rsInfo[0]['SKU'];
						$_SESSION['cartitems'][$pcnt]['ProductSEName'] = $rsInfo[0]['SEName'];
						$_SESSION['cartitems'][$pcnt]['ProductSalePrice'] = 0;
						$_SESSION['cartitems'][$pcnt]['ProductSalePrice2'] = 0;
						$_SESSION['cartitems'][$pcnt]['ProductPrice'] = 0;		
						$_SESSION['cartitems'][$pcnt]['total_price'] = 0;				
						$_SESSION['cartitems'][$pcnt]['ProductQty'] = 1;
						$_SESSION['cartitems'][$pcnt]['ProductWeight'] = $rsInfo[0]['ShipWeight'];												
						$_SESSION['cartitems'][$pcnt]['Type'] = "FreeProd";	
						break;
					}
				}
			}
			header("Location: ".SERVER_ROOT."shoppingcart.html");
			exit;
		}
		else
		{
			if(check_free_promotion_product() && strtoupper($_SESSION['coupon_code']) != "LOVEMOM")
			{
				
				remove_free_promotion_product();
			}
		}
	
	}
	
	if($promoMessage!="")
	{
		echo " ".$promoMessage;
	}
	
	if($cpn=="err")
	{
		echo "<label class='promo_error' style='color:#f00'>Invalid Promo Code.</label>";
	}
?>