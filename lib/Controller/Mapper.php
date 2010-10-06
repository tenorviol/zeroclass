<?php
/**
 * Copyright (c) 2010 Christopher Johnson
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE. 
 */

class Controller_Mapper implements Controller {
	
	private $mapper;
	
	public function __construct(Request_Mapper $mapper) {
		$this->mapper = $mapper;
	}
	
	public function control() {
		switch ($_SERVER['REQUEST_METHOD']) {
		case 'POST':
			$result = $this->post();
			break;
		case 'PUT':
			$result = $this->put();
			break;
		case 'DELETE':
			$result = $this->delete();
			break;
		default:
			$result = $this->get();
		}
		$this->response($result);
	}
	
	protected function get() {
		$request = new Request_Filter($_GET);
		return $this->mapper->requestGet($request);
	}
	
	protected function post() {
		$request = new Request_Filter($_POST);
		return $this->mapper->requestPost($request);
	}
	
	protected function put() {
		parse_str(file_get_contents('php://input'), $_PUT);
		$request = new Request_Filter($_PUT);
		return $this->mapper->requestPost($request);
	}
	
	protected function delete() {
		parse_str(file_get_contents('php://input'), $_DELETE);
		$request = new Request_Filter($_DELETE);
		return $this->mapper->requestDelete($request);
	}
	
	protected function response($result) {
		echo json_encode($result);
	}
}