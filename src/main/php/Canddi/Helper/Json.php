<?php
/*
    This wraps the Zend_Json so that we catch errors better
 */
class Canddi_Helper_Json
{
    /**
     * Decodes the given $encodedValue string which is
     * encoded in the JSON format
     *
     * Uses ext/json's Canddi_Helper_Json::decode if available.
     *
     * @param string $encodedValue Encoded in JSON format
     * @param int $objectDecodeType Optional; flag indicating how to decode
     * objects. See {@link Zend_Canddi_Helper_Json::decoder::decode()} for details.
     * @return mixed
     */
    public static function decode($encodedValue, $objectDecodeType = Zend_Json::TYPE_ARRAY)
    {
        try {
            return Zend_Json::decode($encodedValue, $objectDecodeType);
        } catch (Exception $e) {
            //ok - time for something different
            $encodedValue = stripslashes($encodedValue);
            try {
                return Zend_Json::decode($encodedValue, $objectDecodeType);
            } catch (Exception $f) {
                throw new Canddi_Exception_Fatal_InvalidInput($encodedValue, $f->getMessage());
            }
        }
    }

    /**
     * Encode the mixed $valueToEncode into the JSON format
     *
     * Encodes using ext/json's Canddi_Helper_Json::encode() if available.
     *
     * NOTE: Object should not contain cycles; the JSON format
     * does not allow object reference.
     *
     * NOTE: Only public variables will be encoded
     *
     * NOTE: Encoding native javascript expressions are possible using Zend_Json_Expr.
     *       You can enable this by setting $options['enableJsonExprFinder'] = true
     *
     * @see Zend_Json_Expr
     *
     * @param  mixed $valueToEncode
     * @param  boolean $cycleCheck Optional; whether or not to check for object recursion; off by default
     * @param  array $options Additional options used during encoding
     * @return string JSON encoded object
     */
    public static function encode($valueToEncode, $cycleCheck = false, $options = array())
    {
        $valueToEncodeUTF8Safe = Canddi_Helper::toUTF8($valueToEncode);
        try {
                return Zend_Json::encode($valueToEncodeUTF8Safe, $cycleCheck, $options);
        } catch (Exception $e) {
                throw new Canddi_Exception_Fatal_InvalidInput($valueToEncode, $e->getMessage());
        }
    }

    /**
     * fromXml - Converts XML to JSON
     *
     * Converts a XML formatted string into a JSON formatted string.
     * The value returned will be a string in JSON format.
     *
     * The caller of this function needs to provide only the first parameter,
     * which is an XML formatted String. The second parameter is optional, which
     * lets the user to select if the XML attributes in the input XML string
     * should be included or ignored in xml2json conversion.
     *
     * This function converts the XML formatted string into a PHP array by
     * calling a recursive (protected static) function in this class. Then, it
     * converts that PHP array into JSON by calling the "encode" static funcion.
     *
     * Throws a Canddi_Exception_Fatal_InvalidInput if the input not a XML formatted string.
     * NOTE: Encoding native javascript expressions via Zend_Json_Expr is not possible.
     *
     * @static
     * @access public
     * @param string $xmlStringContents XML String to be converted
     * @param boolean $ignoreXmlAttributes Include or exclude XML attributes in
     * the xml2json conversion process.
     * @return mixed - JSON formatted string on success
     * @throws Canddi_Exception_Fatal_InvalidInput
     */
    public static function fromXml($xmlStringContents, $ignoreXmlAttributes = true)
    {
        try {
                return Zend_Json::fromXml($xmlStringContents, $ignoreXmlAttributes);
        } catch (Exception $e) {
                throw new Canddi_Exception_Fatal_InvalidInput($valueToEncode, $e->getMessage());
        }
    } // End of function fromXml.
    /**
     * Pretty-print JSON string
     *
     * Use 'indent' option to select indentation string - by default it's a tab
     *
     * @param string $json Original JSON string
     * @param array $options Encoding options
     * @return string
     */
    public static function prettyPrint($json, $options = array())
    {
        try {
                return Zend_Json::prettyPrint($json, $options);
        } catch (Exception $e) {
                throw new Canddi_Exception_Fatal_InvalidInput($json, $e->getMessage());
        }
    }
}