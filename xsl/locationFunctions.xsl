<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="2.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template name="country-by-code">
	<xsl:param name="language" select="'en'"/>
	<xsl:param name="form" select="'long'"/>
	<xsl:param name="code"/>
	<xsl:param name="codes"/>

	<xsl:choose>
	<xsl:when test="count($codes) > 0">
		<xsl:value-of select="$codes/country[@id=$code]/language[@id=$language]/form[@id=$form]"/>
	</xsl:when>
	<xsl:otherwise>
		<xsl:value-of select="document('/wwwroot/xxxxxxxxx/canada/xml/countries.xml')/countrycodes/country[@id=$code]/language[@id=$language]/form[@id=$form]"/>
	</xsl:otherwise>
	</xsl:choose>
</xsl:template>

</xsl:stylesheet>
