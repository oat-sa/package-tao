<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">

	<xsl:output method="xml" version="1.0" encoding="utf-8" indent="yes" omit-xml-declaration="yes" />
  <!-- response > form_type (INPUT) -->
	<xsl:template match="response" mode="form_type">
		<xsl:apply-templates select="ancestor::item" mode="currentItemInputType" />
	</xsl:template>

  <!-- response > form -->
	<xsl:template match="response" mode="form">
		<xsl:param name="code2" />
		<xsl:param name="position" />
		<xsl:variable name="type">
			<xsl:apply-templates select="." mode="form_type" />
		</xsl:variable>

		<xsl:variable name="code1">
			<xsl:apply-templates select="." mode="form_code" />
		</xsl:variable>
		<xsl:variable name="code">
			<xsl:choose>
				<xsl:when test="$code2!=''">
					<xsl:value-of select="concat($code1, '_', $code2)" />
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$code1" />
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="code3">
			<xsl:value-of select="code" />
		</xsl:variable>

    <!--<xsl:value-of select="$code" />-->
		<div class="div_input">
			<span class="variable">
				<xsl:if test="$type='slider'">
					<xsl:attribute name="class">
						<xsl:text>variableSlider</xsl:text>
					</xsl:attribute>
				</xsl:if>
				<xsl:value-of select="$code" />
				</span>
				<xsl:choose>
					<xsl:when test="$type='slider'">
						<div>
							<xsl:attribute name="class">
								<xsl:value-of select="'title'" />
							</xsl:attribute>
							<xsl:value-of select="ancestor::item/header[code=$code3]/label" />
						</div>
						<div>
							<xsl:attribute name="class">
								<xsl:value-of select="'slider'" />
							</xsl:attribute>
							<xsl:attribute name="id">
								<xsl:value-of select="$code" />
							</xsl:attribute>
							<input>
								<xsl:attribute name="type">
									<xsl:value-of select="'hidden'" />
								</xsl:attribute>
								<xsl:attribute name="id">
									<xsl:value-of select="concat('value_', $code)" />
								</xsl:attribute>
								<xsl:attribute name="value">
                <!--???-->
									<xsl:text></xsl:text>
								</xsl:attribute>
							</input>
							<input>
								<xsl:attribute name="type">
									<xsl:value-of select="'hidden'" />
								</xsl:attribute>
								<xsl:attribute name="class">
									<xsl:value-of select="'min'" />
								</xsl:attribute>
								<xsl:attribute name="value">
									<xsl:apply-templates select="." mode="minval" />
								</xsl:attribute>
							</input>
							<input>
								<xsl:attribute name="type">
									<xsl:value-of select="'hidden'" />
								</xsl:attribute>
								<xsl:attribute name="class">
									<xsl:value-of select="'max'" />
								</xsl:attribute>
								<xsl:attribute name="value">
									<xsl:apply-templates select="." mode="maxval" />
								</xsl:attribute>
							</input>
							<input>
								<xsl:attribute name="type">
									<xsl:value-of select="'hidden'" />
								</xsl:attribute>
								<xsl:attribute name="class">
									<xsl:value-of select="'step'" />
								</xsl:attribute>
								<xsl:attribute name="value">
									<xsl:apply-templates select="." mode="step" />
								</xsl:attribute>
							</input>
						</div>
						<span>
							<xsl:attribute name="class">
								<xsl:value-of select="'levelMin'" />
							</xsl:attribute>
            <!--@todo to translate, be careful only trnaslation in the xsl preview-->
							Low
						</span>
						<span>
							<xsl:attribute name="class">
								<xsl:value-of select="'levelMax'" />
							</xsl:attribute>
            <!--@todo to translate, be careful only trnaslation in the xsl preview-->
							High
						</span>
					</xsl:when>
					<xsl:when test="$type='text'">
						<input type="text" id="{$code}" name="{$code}">
              <xsl:if test="ancestor::item/header[code=$code2]/@size">
                <xsl:attribute name="class">
                  <xsl:text>inputSize_</xsl:text>
                  <xsl:value-of select="ancestor::item/header[code=$code2]/@size" />
                </xsl:attribute>
              </xsl:if>
            </input>
					</xsl:when>
					<xsl:when test="$type='radio'">
						<input type="radio">
							<xsl:attribute name="name">
								<xsl:call-template name="stripLast">
									<xsl:with-param name="string" select="$code" />
									<xsl:with-param name="char" select="'_'" />
								</xsl:call-template>
							</xsl:attribute>
							<xsl:attribute name="id">
								<xsl:value-of select="$code" />
							</xsl:attribute>
							<xsl:attribute name="value">
								<xsl:value-of select="@code" />
							</xsl:attribute>
						</input>
					</xsl:when>
					<xsl:when test="$type='select'">
            <xsl:variable name="list">
              <xsl:value-of select="@values" />
            </xsl:variable>
            <select name="{$code}" id="{$code}">
              <xsl:apply-templates select="//values[@list=$list]/value" />
            </select>
          </xsl:when>
          <xsl:when test="$type='checkbox'">
            <input type="checkbox">
              <xsl:attribute name="name">
                <xsl:value-of select="$code" />
              </xsl:attribute>
              <xsl:attribute name="value">
                <xsl:value-of select="@code" />
              </xsl:attribute>
            </input>
          </xsl:when>
          <xsl:when test="$type='mixed'">
            <xsl:choose>
              <xsl:when test="$position=1">
                <input type="checkbox">
                  <xsl:attribute name="name">
                    <xsl:value-of select="$code" />
                  </xsl:attribute>
                  <xsl:attribute name="value">
                    <xsl:value-of select="@code" />
                  </xsl:attribute>
                </input>
              </xsl:when>
              <xsl:otherwise>
                <input type="text" id="{$code}" name="{$code}">
                  <xsl:if test="ancestor::item/header[code=$code2]/@size">
                    <xsl:attribute name="class">
                      <xsl:text>inputSize_</xsl:text>
                      <xsl:value-of select="ancestor::item/header[code=$code2]/@size" />
                    </xsl:attribute>
                  </xsl:if>
                </input>
              </xsl:otherwise>
            </xsl:choose>
          </xsl:when>
				</xsl:choose>
			</div>
		</xsl:template>
  <!--option generation for dropdown item-->
  <xsl:template match="value">
    <option>
      <xsl:attribute name="value">
        <xsl:value-of select="code" />
      </xsl:attribute>
      <xsl:value-of select="label" />
    </option>
  </xsl:template>
  <!-- response > form_code -->
		<xsl:template match="response" mode="form_code">
			<xsl:variable name="id">
				<xsl:apply-templates select="ancestor::item" mode="getId" />
			</xsl:variable>
			<xsl:variable name="code">
				<xsl:value-of select="code" />
			</xsl:variable>
			<xsl:value-of select="concat($id, '_', $code)" />
		</xsl:template>

  <!--getItem id-->
		<xsl:template match="item" mode="getId">
			<xsl:choose>
				<xsl:when test="count(ancestor::itemGroup/item)>1">
					<xsl:value-of select="@id" />
				</xsl:when>
      <!--unik item don't have id but take itemgroup id-->
				<xsl:otherwise>
					<xsl:value-of select="ancestor::itemGroup/@id" />
				</xsl:otherwise>
			</xsl:choose>
		</xsl:template>

  <!-- response > form_get_value -->
		<xsl:template match="response" mode="form_get_value">
			<xsl:variable name="type">
				<xsl:apply-templates select="." mode="form_type" />
			</xsl:variable>

			<xsl:if test="$type='text'">
      $(":text[name='<xsl:apply-templates select="." mode="form_code" />']").val()
			</xsl:if>
			<xsl:if test="$type='radio'">
      $(":radio[name='<xsl:apply-templates select="." mode="form_code" />']:checked").val()
			</xsl:if>
			<xsl:if test="$type='checkbox'">
      $(":checkbox[name='<xsl:apply-templates select="." mode="form_code" />']:checked").val()
			</xsl:if>
			<xsl:if test="$type='slider'">
      get_slider_value('<xsl:apply-templates select="." mode="form_code" />')
			</xsl:if>
		</xsl:template>
  <!-- item > form_get_value (textarea) -->
		<xsl:template match="item" mode="form_get_value">
			$("textarea[name='
			<xsl:value-of select="@id" />']").val()
		</xsl:template>

	</xsl:stylesheet>