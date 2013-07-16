<?xml version="1.0" encoding="UTF-8"?>
<!--
	@author Paul van der Meijs <code@paulvandermeijs.nl>
	@copyright Copyright (c) 2013, Paul van der Meijs
	@version 1.0
 -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">


	<!-- Languages -->
	
	<xsl:template match="languages">
		<dl class="languages">
			<dt class="active">
				<xsl:apply-templates select="language[1 = number(active)]" />
			</dt>
			<dd class="inactive">
				<ul>
					<xsl:apply-templates select="language[0 = number(active)]" mode="inactive" />
				</ul>
			</dd>
		</dl>
	</xsl:template>


	<!-- Language -->

	<xsl:template match="language">
		<a href="{url}">
			<xsl:attribute name="class">
				<xsl:text>language language-</xsl:text><xsl:value-of select="@lang" />
				<xsl:if test="1 = number(active)">
					<xsl:text> active</xsl:text>
				</xsl:if>
			</xsl:attribute>
			<img src="{country_flag_url}" alt="" class="flag" /><span class="native_name"><xsl:value-of select="native_name" /></span> <span class="translated_name"><xsl:value-of select="translated_name" /></span>
		</a>
	</xsl:template>

	<xsl:template match="language" mode="inactive">
		<li>
			<xsl:apply-templates select="." />
		</li>
	</xsl:template>

</xsl:stylesheet>