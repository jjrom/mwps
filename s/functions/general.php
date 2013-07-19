<?php
/*
 * mapshup - Webmapping made easy
 * http://mapshup.info
 *
 * Copyright Jérôme Gasperi, 2011.12.08
 *
 * jerome[dot]gasperi[at]gmail[dot]com
 *
 * This software is a computer program whose purpose is a webmapping application
 * to display and manipulate geographical data.
 *
 * This software is governed by the CeCILL-B license under French law and
 * abiding by the rules of distribution of free software.  You can  use,
 * modify and/ or redistribute the software under the terms of the CeCILL-B
 * license as circulated by CEA, CNRS and INRIA at the following URL
 * "http://www.cecill.info".
 *
 * As a counterpart to the access to the source code and  rights to copy,
 * modify and redistribute granted by the license, users are provided only
 * with a limited warranty  and the software's author,  the holder of the
 * economic rights,  and the successive licensors  have only  limited
 * liability.
 *
 * In this respect, the user's attention is drawn to the risks associated
 * with loading,  using,  modifying and/or developing or reproducing the
 * software by the user in light of its specific status of free software,
 * that may mean  that it is complicated to manipulate,  and  that  also
 * therefore means  that it is reserved for developers  and  experienced
 * professionals having in-depth computer knowledge. Users are therefore
 * encouraged to load and test the software's suitability as regards their
 * requirements in conditions enabling the security of their systems and/or
 * data to be ensured and,  more generally, to use and operate it in the
 * same conditions as regards security.
 *
 * The fact that you are presently reading this means that you have had
 * knowledge of the CeCILL-B license and that you accept its terms.
 */

/**
 * Avoid proxy.php hacks :)
 * 
 * @param <type> $request
 * @return <type> bolean
 */
function abcCheck($request) {
    if (!isset($request["a"]) || !isset($request["b"]) || !isset($request["c"])) {
        return false;
    }
    $a = $request["a"];
    $b = $request["b"];
    $c = $request["c"];
    if (is_numeric($a) && is_numeric($b) && is_numeric($c)) {
        if ((intval($a) + 17) - 3 * (intval($b) - 2) == intval($c)) {
            return true;
        }
    }
    return false;
}

/**
 * Get Remote data from url using curl
 * @param <String> $url : input url to send GET request
 * @param <String> $useragent : useragent modification
 * @param <boolean> $info : set to true to return transfert info
 *
 * @return either a stringarray containing data and info if $info is set to true
 */
function getRemoteData($url, $useragent, $info) {
    if (!empty($url)) {
        $curl = initCurl($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        if ($useragent != null) {
            curl_setopt($curl, CURLOPT_USERAGENT, $useragent);
        }
        $theData = curl_exec($curl);
        $info == true ? $theInfo = curl_getinfo($curl) : "";
        curl_close($curl);
        return $info == true ? array("data" => $theData, "info" => $theInfo) : $theData;
    }
    return $info == true ? array("data" => "", "info" => "") : "";
}

/**
 * Set the proxy if needed
 * @param <type> $url Input url to proxify
 */
function initCurl($url) {

    /**
     * Init curl
     */
    $curl = curl_init();

    /**
     * If url is on the same domain name server
     * as _msprowser application, it is accessed directly
     * (i.e. no use of CURL proxy)
     */
    if ((substr($url, 0, 16) != "http://localhost") && (stristr($url, MSP_DOMAIN) === FALSE)) {
        if (MSP_USE_PROXY) {
            curl_setopt($curl, CURLOPT_PROXY, MSP_PROXY_URL);
            curl_setopt($curl, CURLOPT_PROXYPORT, MSP_PROXY_PORT);
            curl_setopt($curl, CURLOPT_PROXYUSERPWD, MSP_PROXY_USER . ":" . MSP_PROXY_PASSWORD);
        }
    }

    return $curl;
}

/**
 * Get Remote data from url using curl
 * @param <String> $url
 */
function postRemoteData($url, $request, $setHeaders) {

    if (!empty($url)) {
        $curl = initCurl($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        if ($setHeaders) {
            
            // if $setHeaders is a boolean then add default HTTPHEADERS
            if (is_bool($setHeaders) === true) {
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'POST HTTP/1.1',
                    'Content-type: text/xml;charset="UTF-8"',
                    'Accept: text/xml',
                    'Cache-Control: no-cache',
                    'Pragma: no-cache',
                    'Expect:'
                ));
            }
            // if $setHeaders is an array then set HTTPHEADERS with $setHeaders content
            else if (is_array($setHeaders) === true) {
                curl_setopt($curl, CURLOPT_HTTPHEADER, $setHeaders);
            }
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        $theData = curl_exec($curl);
        curl_close($curl);
        return $theData;
    }
    return "";
}

?>