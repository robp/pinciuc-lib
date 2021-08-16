<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="2.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:template name="upper-case">
    <xsl:param name="string"/>
    <xsl:copy-of select="translate($string, 'abcdefghijklmnopqrstuvwxyz', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ')"/>
  </xsl:template>

  <xsl:template name="lower-case">
    <xsl:param name="string"/>
    <xsl:copy-of select="translate($string, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')"/>
  </xsl:template>

  <xsl:template name="trim-to-chars">
    <xsl:param name="string" select="''"/>
    <xsl:param name="result_string" select="''"/>
    <xsl:param name="num_chars" select="number(20)"/>

    <xsl:choose>
      <xsl:when test="string-length(normalize-space($string)) = 0">
        <xsl:copy-of select="$result_string"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:variable name="temp_string" select="concat($string, ' ')"/>
        <xsl:variable name="new_string" select="normalize-space(concat($result_string, ' ', substring-before($temp_string, ' ')))"/>

        <xsl:choose>
          <xsl:when test="string-length($new_string) > $num_chars">
            <xsl:copy-of select="concat($result_string, '...')"/>
          </xsl:when>
          <xsl:otherwise>
            <xsl:call-template name="trim-to-chars">
              <xsl:with-param name="string" select="substring-after($string, ' ')"/>
              <xsl:with-param name="result_string" select="$new_string"/>
              <xsl:with-param name="num_chars" select="$num_chars"/>
            </xsl:call-template>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template name="strip-tags">
    <xsl:param name="string" select="''"/>
    <xsl:param name="result_string" select="''"/>
    <xsl:param name="open" select="0"/>

    <xsl:choose>
    <xsl:when test="$open = 0">
      <xsl:choose>
      <xsl:when test="contains($string, '&lt;')">
        <xsl:variable name="new_string" select="concat($result_string, substring-before($string, '&lt;'))"/>
        <xsl:call-template name="strip-tags">
          <xsl:with-param name="string" select="substring-after($string, '&lt;')"/>
          <xsl:with-param name="result_string" select="$new_string"/>
          <xsl:with-param name="open" select="1"/>
        </xsl:call-template>
      </xsl:when>
      <xsl:otherwise>
        <xsl:copy-of select="$result_string"/>
      </xsl:otherwise>
      </xsl:choose>
    </xsl:when>
    <xsl:otherwise>
      <xsl:choose>
      <xsl:when test="contains($string, '&gt;')">
        <xsl:call-template name="strip-tags">
          <xsl:with-param name="string" select="substring-after($string, '&gt;')"/>
          <xsl:with-param name="result_string" select="$result_string"/>
          <xsl:with-param name="open" select="0"/>
        </xsl:call-template>
      </xsl:when>
      <xsl:otherwise>
        <xsl:copy-of select="$result_string"/>
      </xsl:otherwise>
      </xsl:choose>
    </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template name="lf2br">
    <xsl:param name="string"/>
    <xsl:choose>
      <xsl:when test="contains($string,'&#xA;')">
        <xsl:value-of select="substring-before($string,'&#xA;')" disable-output-escaping="yes"/><br/>
        <xsl:call-template name="lf2br">
          <xsl:with-param name="string">
            <xsl:value-of select="substring-after($string,'&#xA;')" disable-output-escaping="yes"/>
          </xsl:with-param>
        </xsl:call-template>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="$string" disable-output-escaping="yes"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template name="escape-apos">
     <xsl:param name="string"/>
     <xsl:variable name="apos">'</xsl:variable>
     <xsl:choose>
        <xsl:when test="contains($string, $apos)">
           <xsl:copy-of select="substring-before($string, $apos)"/>
           <xsl:text>\'</xsl:text>
           <xsl:call-template name="escape-apos">
              <xsl:with-param name="string" select="substring-after($string, $apos)"/>
           </xsl:call-template>
        </xsl:when>
        <xsl:otherwise>
           <xsl:copy-of select="$string"/>
        </xsl:otherwise>
     </xsl:choose>
  </xsl:template>

  <xsl:template name="trim">
    <xsl:param name="string" />
    <xsl:param name="char" select="' '"/>
    
    <xsl:choose>
      <xsl:when test="starts-with($string, $char)">
        <xsl:call-template name="trim">
          <xsl:with-param name="string" select="substring-after($string, $char)" />
          <xsl:with-param name="char" select="$char" />
        </xsl:call-template>
      </xsl:when>
      <xsl:when test="substring($string, string-length($string) - string-length($char) + 1) = $char">
        <xsl:call-template name="trim">
          <xsl:with-param name="string" select="substring($string, 1, string-length($string) - string-length($char))" />
          <xsl:with-param name="char" select="$char" />
        </xsl:call-template>
      </xsl:when>
      <xsl:otherwise>
        <xsl:copy-of select="$string" />
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template name="normalize">
    <xsl:param name="string" />
    <xsl:param name="char" select="' '"/>

    <xsl:choose>
      <xsl:when test="contains($string, concat($char, $char))">
        <xsl:call-template name="normalize">
          <xsl:with-param name="string" select="concat(substring-before($string, concat($char, $char)), $char, substring-after($string, concat($char, $char)))" />
        </xsl:call-template>
      </xsl:when>
      <xsl:otherwise>
        <xsl:call-template name="trim">
          <xsl:with-param name="string" select="$string"/>
          <xsl:with-param name="char" select="$char"/>
        </xsl:call-template>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

</xsl:stylesheet>
