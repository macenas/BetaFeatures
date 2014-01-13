<?php
/*
 * This file is part of the MediaWiki extension BetaFeatures.
 *
 * BetaFeatures is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * BetaFeatures is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with BetaFeatures.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HTML auto-enroll field
 *
 * @file
 * @ingroup Extensions
 * @copyright 2013 Mark Holmquist and others; see AUTHORS
 * @license GNU General Public License version 2 or later
 */

class HTMLAutoEnrollField extends NewHTMLCheckField {

	const OPTION_ENABLED = '1';
	const OPTION_DISABLED = '0';

	function __construct( $options ) {
		// We need the new checkbox style to have a sane-looking field
		$options['mw-ui'] = true;
		parent::__construct( $options );
	}

	function getHeaderHTML( $value ) {
		$html = Html::openElement( 'div', array(
			'class' => 'mw-ui-autoenroll-header',
		) );

		$html .= Html::openElement( 'div', array(
			'class' => 'mw-ui-feature-title-contain',
		) );

		$html .= Html::rawElement( 'p', array(
			//'class' => 'mw-ui-feature-title',
		), $this->getPostCheckboxLabelHTML() );

		// Close -title-contain
		$html .= Html::closeElement( 'div' );

		// Close -header
		$html .= Html::closeElement( 'div' );

		return $html;
	}

	function getMainHTML( $value ) {
		$parent = $this->mParent;

		$html = Html::openElement( 'div', array(
			'class' => 'mw-ui-feature-main',
		) );

		$html .= Html::openElement( 'div', array(
			'class' => 'mw-ui-feature-meta',
		) );

		if ( isset( $this->mParams['user-count'] ) ) {
			$userCountMsg = 'mw-ui-feature-user-count';

			if ( isset( $this->mParams['user-count-msg'] ) ) {
				$userCountMsg = $this->mParams['user-count-msg'];
			}

			$html .= Html::rawElement(
				'p',
				array( 'class' => 'mw-ui-feature-user-count' ),
				$parent->msg( $userCountMsg )->numParams( $this->mParams['user-count'] )->escaped()
			);

			$attrs['data-count'] = $this->mParams['user-count'];
		}

		// Close -meta
		$html .= Html::closeElement( 'div' );

		// Close -main
		$html .= Html::closeElement( 'div' );

		return $html;
	}

	function getInputHTML( $value ) {
		$html = '';

		$attrs = $this->getTooltipAndAccessKey();
		$attrs['id'] = $this->mID;
		$attrs['class'] = 'mw-ui-feature-toggle';

		$divClasses = array(
			'mw-ui-autoenroll-field',
		);

		// Use 'cssclass' to populate this. Separate from 'class', of course.
		if ( $this->mClass !== '' ) {
			$divClasses[] = $this->mClass;
		}

		if ( isset( $this->mParams['disabled'] ) &&
				$this->mParams['disabled'] === true ) {
			$attrs['disabled'] = true;
		}

		$html .= Html::openElement( 'div', array(
			'class' => implode( ' ', $divClasses ),
		) );

		$html .= Html::rawElement( 'div', array(
			'class' => 'mw-ui-feature-checkbox',
		), $this->getCheckboxHTML( $value, $attrs ) );

		$html .= Html::openElement( 'div', array(
			'class' => 'mw-ui-feature-contain',
		) );

		$html .= $this->getHeaderHTML( $value );
		$html .= $this->getMainHTML( $value );

		// mw-ui-feature-contain
		$html .= Html::closeElement( 'div' );

		// mw-ui-feature-field
		$html .= Html::closeElement( 'div' );

		return $html;
	}

	/**
	 * Override to use integers, so we don't lose the database rows on
	 * unset...
	 */
	function loadDataFromRequest( $request ) {
		$res = parent::loadDataFromRequest( $request );

		if ( $res === true ) {
			return HTMLFeatureField::OPTION_ENABLED;
		} else if ( $res === false ) {
			return HTMLFeatureField::OPTION_DISABLED;
		} else {
			// Dunno what happened, but I'm not gonna fight it.
			return $res;
		}
	}
}
