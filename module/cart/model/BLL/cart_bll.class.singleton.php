<?php
	class cart_bll {
		private $dao;
		private $db;
		static $_instance;

		function __construct() {
			$this -> dao = cart_dao::getInstance();
			$this -> db = db::getInstance();
		}

		public static function getInstance() {
			if (!(self::$_instance instanceof self)) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		public function get_insert_cart_BLL($args) {
			// return $args;

			$token_dec = middleware_auth::decode_token('access', $args[1]);
			$checkCart = $this -> dao -> select_lineCart($this->db, $args[0], $token_dec['uid']);
			// return $checkCart;

			// Comprobar que la linea del carrito no existe, entonces inserta
			if (!$checkCart) {
				$rdo = $this -> dao -> insert_cart($this->db, $args[0], $token_dec['uid']);
				$data_re = $this -> dao -> select_realestate($this->db, $args[0]);
				$data_qty = $this -> dao -> select_totalQty($this->db, $token_dec['uid']);

				if ($data_qty) {
					$qty_value = get_object_vars($data_qty); //serializa objeto
				}else {
					return "error_cart";
				}

				$data = array("msg" => "insert done", "re" => $data_re, "qty" => $qty_value['quantity']);
				return $data;
			} else { // Comprobar que la linea del carrito existe, entonces actualiza
				$cart_value = get_object_vars($checkCart); //serializa objeto
				
				$data_re = $this -> dao -> select_realestate($this->db, $args[0]);
				
				if ($data_re) {
					$re_value = get_object_vars($data_re); //serializa objeto
				}else {
					return "error_realestate";
				}

				// return [$cart_value['quantity'], $re_value['stock']];
				if ($cart_value['quantity'] < $re_value['stock']) { // Comprobar que hay suficiente stock
					$rdo = $this -> dao -> update_cart($this->db, $args[0], $token_dec['uid'], 1);
					$data_qty = $this -> dao -> select_totalQty($this->db, $token_dec['uid']);

					if ($data_qty) {
						$qty_value = get_object_vars($data_qty); //serializa objeto
					}else {
						return "error_cart";
					}

					$data = array("msg" => "update done", "re" => $data_re, "qty" => $qty_value['quantity']);
					return $data;
				} else {
					return "insuficient stock";
				}
			}
		}

		public function get_select_cart_BLL($token) {
			// return $token;

			$token_dec = middleware_auth::decode_token('access', $token);
			$cart = $this -> dao -> select_cart($this->db, $token_dec['uid']);

			if ($cart) {
				return $cart;
			} else {
				return "no_cart";
			}
		}

		public function get_update_cart_BLL($args) {
			// return $args;

			$token_dec = middleware_auth::decode_token('access', $args[1]);

			// elimina linea del carrito
			if ($args[3] == "delete") {
				$rdo = $this -> dao -> delete_lineCart($this->db, $args[0], $token_dec['uid']);

				if (!$rdo) {
					return "error_cart";
				}

				$cart = $this -> dao -> select_cart($this->db, $token_dec['uid']);

				if ($cart) {
					return [$cart, "delete", $args[0]];
				} else {
					return "no_cart";
				}
			} else {
				// recupera stock del producto
				$data_re = $this -> dao -> select_realestate($this->db, $args[0]);
				
				if ($data_re) {
					$re_value = get_object_vars($data_re); //serializa objeto
				}else {
					return "error_realestate";
				}

				// actualiza linea del carrito
				if ((($args[2] + $args[3]) <= $re_value['stock']) && (($args[2] + $args[3]) >= 1)) {  // Comprobar que hay suficiente stock, y la cantidad final no sea inferior a 1
					$rdo = $this -> dao -> update_cart($this->db, $args[0], $token_dec['uid'], $args[3]);

					if (!$rdo) {
						return "error_cart";
					}

					$cart = $this -> dao -> select_cart($this->db, $token_dec['uid']);

					if ($cart) {
						return [$cart, "update"];
					} else {
						return "error_cart";
					}
				}
			}
		}

		public function get_insert_bill_BLL($token) {
			$token_dec = middleware_auth::decode_token('access', $token);
			$bill = $this -> dao -> insert_bill($this->db, $token_dec['uid']);

			if ($bill) {
				$data = array("uid" => $token_dec['uid'], "id_bill" => $bill->id_bill);
				return $data;
			} else {
				return "error_bill";
			}
		}

		public function get_insert_bill_detail_BLL($data) {
			$bill_detail = $this -> dao -> insert_bill_detail($this->db, $data['uid'], $data['id_bill']);
			
			if ($bill_detail) {
				return $bill_detail;
			} else {
				return "error_bill_detail";
			}
		}

		public function get_update_stock_BLL($uid) {
			$stock = $this -> dao -> update_stock($this->db, $uid);
			
			if ($stock) {
				return $stock;
			} else {
				return "error_stock";
			}
		}

		public function get_insert_purchase_log_BLL($uid) {
			$purchase_log = $this -> dao -> insert_purchase_log($this->db, $uid);
			
			if ($purchase_log) {
				return $purchase_log;
			} else {
				return "error_purchase_log";
			}
		}

		public function get_delete_cart_BLL($uid) {
			$del_cart = $this -> dao -> delete_cart($this->db, $uid);
			
			if ($del_cart) {
				return "done";
			} else {
				return "error_cart";
			}
		}
	}