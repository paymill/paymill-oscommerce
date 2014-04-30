<?php
define("MODULE_PAYMENT_PAYMILL_ELV_TEXT_PUBLIC_TITLE", "D&eacute;bito direto");
define("MODULE_PAYMENT_PAYMILL_ELV_STATUS_TITLE", "Ativar");
define("MODULE_PAYMENT_PAYMILL_ELV_DESCRIPTION", "Registo PAYMILL");
define("MODULE_PAYMENT_PAYMILL_ELV_TRANSACTION_ORDER_STATUS_ID_TITLE", "Estado da Transac&ccedil;&atilde;o da Encomenda");
define("MODULE_PAYMENT_PAYMILL_ELV_TRANSACTION_ORDER_STATUS_ID_DESC", "Incluir dados sobre a transac&ccedil;&atilde;o neste n&iacute;vel do estado da encomenda");
define("MODULE_PAYMENT_PAYMILL_ELV_FASTCHECKOUT_TITLE", "Ativar o checkout r&aacute;pido");
define("MODULE_PAYMENT_PAYMILL_ELV_FASTCHECKOUT_DESC", "Se ativado, os dados dos seus clientes ser&atilde;o armazenados pela PAYMILL e disponibilizados novamente para compras futuras. O cliente ter&aacute; apenas de introduzir os seus dados uma vez. Esta solu&ccedil;&atilde;o &eacute; compat&iacute;vel com o PCI.");
define("MODULE_PAYMENT_PAYMILL_ELV_WEBHOOKS_TITLE", "Permitir Webhooks");
define("MODULE_PAYMENT_PAYMILL_ELV_WEBHOOKS_DESC", "Sincronizar os meus reembolsos do PAYMILL Cockpit com a conta da minha loja automaticamente");
define("MODULE_PAYMENT_PAYMILL_ELV_WEBHOOKS_LINK_CREATE", "Criar Webhooks");
define("MODULE_PAYMENT_PAYMILL_ELV_WEBHOOKS_LINK_REMOVE", "Remover Webhooks");
define("MODULE_PAYMENT_PAYMILL_CC_WEBHOOKS_LINK", "Criar Webhooks");
define("MODULE_PAYMENT_PAYMILL_ELV_SORT_ORDER_TITLE", "Sequ&ecirc;ncia");
define("MODULE_PAYMENT_PAYMILL_ELV_SORT_ORDER_DESC", "Posi&ccedil;&atilde;o do mostrador durante o checkout.");
define("MODULE_PAYMENT_PAYMILL_ELV_PRIVATEKEY_TITLE", "Chave privada");
define("MODULE_PAYMENT_PAYMILL_ELV_PRIVATEKEY_DESC", "Voc&ecirc; pode encontrar a sua chave privada no cockpit da PAYMILL.");
define("MODULE_PAYMENT_PAYMILL_ELV_PUBLICKEY_TITLE", "Chave p&uacute;blica");
define("MODULE_PAYMENT_PAYMILL_ELV_PUBLICKEY_DESC", "Voc&ecirc; pode encontrar a sua chave p&uacute;blica no cockpit da PAYMILL.");
define("MODULE_PAYMENT_PAYMILL_ELV_LOGGING_TITLE", "Ativar o registo.");
define("MODULE_PAYMENT_PAYMILL_ELV_LOGGING_DESC", "Se ativado, a informa&ccedil;&atilde;o relativa ao progresso da ordem de processamento ser&aacute; gravada no registo.");
define("MODULE_PAYMENT_PAYMILL_ELV_ORDER_STATUS_ID_TITLE", "Estado da Transac&ccedil;&atilde;o da Encomenda");
define("MODULE_PAYMENT_PAYMILL_ELV_ORDER_STATUS_ID_DESC", "Incluir dados sobre a transac&ccedil;&atilde;o neste n&iacute;vel do estado da encomenda");
define("MODULE_PAYMENT_PAYMILL_ELV_ZONE_TITLE", "Zonas Autorizadas");
define("MODULE_PAYMENT_PAYMILL_ELV_ZONE_DESC", "Introduza as zonas autorizadas para este m&oacute;dulo individualmente ( por exemplo: US, UK (deixar em branco para autorizar todas as zonas))");
define("MODULE_PAYMENT_PAYMILL_ELV_ALLOWED_TITLE", "Pa&iacute;ses aceites");
define("MODULE_PAYMENT_PAYMILL_ELV_ALLOWED_DESC", "Se nada foi selecionado, todos os pa&iacute;ses ser&atilde;o aceites");
define("MODULE_PAYMENT_PAYMILL_ELV_TRANS_ORDER_STATUS_ID_TITLE", "Estado da Transac&ccedil;&atilde;o da Encomenda");
define("MODULE_PAYMENT_PAYMILL_ELV_TRANS_ORDER_STATUS_ID_DESC", "Incluir dados sobre a transac&ccedil;&atilde;o neste n&iacute;vel do estado da encomenda");
define("MODULE_PAYMENT_PAYMILL_ELV_TEXT_ACCOUNT", "N&uacute;mero da conta");
define("MODULE_PAYMENT_PAYMILL_ELV_TEXT_BANKCODE", "C&oacute;digo do banco");
define("MODULE_PAYMENT_PAYMILL_ELV_TEXT_ACCOUNT_HOLDER", "Titular da conta");
define("MODULE_PAYMENT_PAYMILL_ELV_TEXT_ACCOUNT_HOLDER_INVALID", "Por favor, introduza o nome do titular da conta de d&eacute;bito direto");
define("MODULE_PAYMENT_PAYMILL_ELV_TEXT_ACCOUNT_INVALID", "Por favor, introduza um n&uacute;mero de conta de d&eacute;bito direto v&aacute;lido");
define("MODULE_PAYMENT_PAYMILL_ELV_TEXT_BANKCODE_INVALID", "Por favor, introduza um c&oacute;digo banc&aacute;rio de d&eacute;bito direto v&aacute;lido.");
define("MODULE_PAYMENT_PAYMILL_ELV_SEPA_TITLE", "Mostrar formul&aacute;rio SEPA");
define("MODULE_PAYMENT_PAYMILL_ELV_SEPA_DESC", "Actualmente apenas s&atilde;o suportados dados banc&aacute;rios provenientes da Alemanha");
define("MODULE_PAYMENT_PAYMILL_ELV_TEXT_BIC", "BIC");
define("MODULE_PAYMENT_PAYMILL_ELV_TEXT_IBAN", "IBAN");
define("MODULE_PAYMENT_PAYMILL_ELV_TEXT_IBAN_INVALID", "Por favor insira um IBAN v&aacute;lido");
define("MODULE_PAYMENT_PAYMILL_ELV_TEXT_BIC_INVALID", "Por favor insira um BIC v&aacute;lido");
define("PAYMILL_10001", "General undefined response.");
define("PAYMILL_10002", "Still waiting on something.");
define("PAYMILL_20000", "General success response.");
define("PAYMILL_40000", "General problem with data.");
define("PAYMILL_40001", "General problem with payment data.");
define("PAYMILL_40100", "Problem with credit card data.");
define("PAYMILL_40101", "Problem with cvv.");
define("PAYMILL_40102", "Card expired or not yet valid.");
define("PAYMILL_40103", "Limit exceeded.");
define("PAYMILL_40104", "Card invalid.");
define("PAYMILL_40105", "Expiry date not valid.");
define("PAYMILL_40106", "Credit card brand required.");
define("PAYMILL_40200", "Problem with bank account data.");
define("PAYMILL_40201", "Bank account data combination mismatch.");
define("PAYMILL_40202", "User authentication failed.");
define("PAYMILL_40300", "Problem with 3d secure data.");
define("PAYMILL_40301", "Currency / amount mismatch");
define("PAYMILL_40400", "Problem with input data.");
define("PAYMILL_40401", "Amount too low or zero.");
define("PAYMILL_40402", "Usage field too long.");
define("PAYMILL_40403", "Currency not allowed.");
define("PAYMILL_50000", "General problem with backend.");
define("PAYMILL_50001", "Country blacklisted.");
define("PAYMILL_50100", "Technical error with credit card.");
define("PAYMILL_50101", "Error limit exceeded.");
define("PAYMILL_50102", "Card declined by authorization system.");
define("PAYMILL_50103", "Manipulation or stolen card.");
define("PAYMILL_50104", "Card restricted");
define("PAYMILL_50105", "Invalid card configuration data.");
define("PAYMILL_50200", "Technical error with bank account.");
define("PAYMILL_50201", "Card blacklisted.");
define("PAYMILL_50300", "Technical error with 3D secure.");
define("PAYMILL_50400", "Decline because of risk issues.");
define("PAYMILL_50500", "General timeout.");
define("PAYMILL_50501", "Timeout on side of the acquirer.");
define("PAYMILL_50502", "Risk management transaction timeout");
define("PAYMILL_50600", "Duplicate transaction.");
define("PAYMILL_INTERNAL_SERVER_ERROR", "The communication with the psp failed.");
define("PAYMILL_INVALID_PUBLIC_KEY", "The public key is invalid.");
define("PAYMILL_INVALID_PAYMENT_DATA", "Paymentmethod, card type currency or country not authorized");
define("PAYMILL_UNKNOWN_ERROR", "Unknown Error");
define("PAYMILL_FIELD_INVALID_AMOUNT_INT", "Missing amount for 3-D Secure");
define("PAYMILL_FIELD_INVALID_AMOUNT", "Missing amount for 3-D Secure");
define("PAYMILL_FIELD_INVALID_CURRENCY", "Invalid currency for 3-D Secure");
define("PAYMILL_FIELD_INVALID_ACCOUNT_NUMBER", "Invalid Account Number");
define("PAYMILL_FIELD_INVALID_ACCOUNT_HOLDER", "Invalid Account Holder");
define("PAYMILL_FIELD_INVALID_BANK_CODE", "Invalid bank code");
define("PAYMILL_FIELD_INVALID_IBAN", "Invalid IBAN");
define("PAYMILL_FIELD_INVALID_BIC", "Invalid BIC");
define("PAYMILL_FIELD_INVALID_COUNTRY", "Invalid country for sepa transactions");
define("PAYMILL_FIELD_INVALID_BANK_DATA", "Invalid bank data");
define("PAYMILL_0", "Ocorreu um erro durante o processamento do seu pagamento.");
define("MODULE_PAYMENT_PAYMILL_ELV_TEXT_TITLE", "PAYMILL D&eacute;bito direto");
define("TEXT_INFO_API_VERSION", "API Version");
define("MODULE_PAYMENT_PAYMILL_ELV_STATUS_DESC", "");
define("SEPA_DRAWN_TEXT", "The direct debit is drawn to the following date: ");
?>