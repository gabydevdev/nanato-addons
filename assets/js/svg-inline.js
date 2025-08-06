/**
 * SVG Inline Replacement Script
 * Replaces img tags with target class with inline SVG code
 */
(function() {
    'use strict';

    let svgReplacements = 0;
    // Get settings from localized data or fallback to defaults
    const settings = (typeof nanatoaddonsSvg !== 'undefined') ? nanatoaddonsSvg : {};
    const targetClass = settings.targetClass || 'style-svg';
    const forceInline = settings.forceInline || false;

    /**
     * Replace img tag with inline SVG
     */
    function replaceSVGImage(img) {
        if (!img.src || !img.src.match(/\.svg$/i)) {
            return;
        }

        // Create XMLHttpRequest to fetch SVG
        const xhr = new XMLHttpRequest();
        xhr.open('GET', img.src, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    // Parse the SVG response
                    const parser = new DOMParser();
                    const svgDoc = parser.parseFromString(xhr.responseText, 'image/svg+xml');
                    const svg = svgDoc.querySelector('svg');

                    if (svg) {
                        // Preserve img attributes
                        const imgID = img.id;
                        const imgClass = img.className;
                        const imgAlt = img.alt;

                        // Set ID if img had one
                        if (imgID) {
                            svg.setAttribute('id', imgID);
                        }

                        // Preserve and extend classes
                        let svgClass = svg.getAttribute('class') || '';
                        if (imgClass) {
                            // Remove the target class and add other classes
                            const cleanedClasses = imgClass.replace(targetClass, '').trim();
                            svgClass = (svgClass + ' ' + cleanedClasses + ' replaced-svg svg-replaced-' + svgReplacements).trim();
                        } else {
                            svgClass = (svgClass + ' replaced-svg svg-replaced-' + svgReplacements).trim();
                        }
                        svg.setAttribute('class', svgClass);

                        // Add alt text as title if present
                        if (imgAlt && !svg.querySelector('title')) {
                            const title = document.createElement('title');
                            title.textContent = imgAlt;
                            svg.insertBefore(title, svg.firstChild);
                        }

                        // Remove potentially problematic attributes
                        svg.removeAttribute('xmlns:a');

                        // Replace the img with the SVG
                        img.parentNode.replaceChild(svg, img);
                        svgReplacements++;

                        // Trigger custom event
                        const event = new CustomEvent('svgReplaced', {
                            detail: { svg: svg, replacements: svgReplacements }
                        });
                        document.dispatchEvent(event);
                    }
                } catch (error) {
                    console.warn('SVG replacement failed:', error);
                }
            }
        };
        xhr.send();
    }

    /**
     * Find and replace all SVG images with target class
     */
    function replaceSVGImages() {
        let selector;
        
        if (forceInline) {
            // Force mode: target ALL img tags with SVG sources
            selector = 'img[src$=".svg"]';
        } else {
            // Normal mode: only target images with the specific class
            selector = 'img.' + targetClass;
        }
        
        const images = document.querySelectorAll(selector);
        images.forEach(function(img) {
            // In force mode, add the target class if it doesn't exist
            if (forceInline && !img.classList.contains(targetClass)) {
                img.classList.add(targetClass);
            }
            replaceSVGImage(img);
        });
    }

    /**
     * Initialize SVG replacement
     */
    function init() {
        // Replace on DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', replaceSVGImages);
        } else {
            replaceSVGImages();
        }

        // Also replace any dynamically added images
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        let imagesToProcess = [];
                        
                        // Check if the added node itself is an img
                        if (node.tagName === 'IMG') {
                            if (forceInline && node.src && node.src.match(/\.svg$/i)) {
                                if (!node.classList.contains(targetClass)) {
                                    node.classList.add(targetClass);
                                }
                                imagesToProcess.push(node);
                            } else if (node.classList.contains(targetClass)) {
                                imagesToProcess.push(node);
                            }
                        }
                        
                        // Check for img tags within the added node
                        if (node.querySelectorAll) {
                            let selector;
                            if (forceInline) {
                                selector = 'img[src$=".svg"]';
                            } else {
                                selector = 'img.' + targetClass;
                            }
                            
                            const svgImages = node.querySelectorAll(selector);
                            svgImages.forEach(function(img) {
                                if (forceInline && !img.classList.contains(targetClass)) {
                                    img.classList.add(targetClass);
                                }
                                imagesToProcess.push(img);
                            });
                        }
                        
                        // Process all found images
                        imagesToProcess.forEach(replaceSVGImage);
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    // Start the magic
    init();

})();
