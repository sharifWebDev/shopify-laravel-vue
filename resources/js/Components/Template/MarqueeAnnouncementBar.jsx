import React, { useEffect, useState } from "react";
import useAnnouncementBarColor from "@/Hooks/useAnnouncementBarColor";
import useSimpleBarStore from "@/DataStores/SimpleBarStore";


const MarqueeAnnouncementBar = () => {
    const {
        type,
        device,
        crossButton,
        buttonFontSelect,

        designFontType,
        designFontSize,
        announcementContent,
        designFontName,
        designFontColor,
        heightBarRange,
        bgSection,
        barBackgroundColor,
        barPosition,
        barPositionFixed,
        backgroundPattern,
        backgroundGradientPosition,
        openInNewTab,
        buttonLink,
        setButtonBackgroundColor,
        bgGradientA,
        bgGradientB,
        marqueeDirection,
        marqueeSpeed
    } = useSimpleBarStore((state) => state);





    const handleNewTabButtonUrl = () => {
        openInNewTab
            ? window.open(buttonLink, "_blank", "noopener,noreferrer")
            : window.open(buttonLink, "_parent");
    };


    const [gradient, setGradient] = useState({
        pointA: bgGradientA,
        pointB: bgGradientB,
    });

    useEffect(() => {
        setGradient({
            pointA: bgGradientA,
            pointB: bgGradientB,
        });
    }, [bgGradientA, bgGradientB]);

    useEffect(() => {
        // shop now button default color set 
        if (type == 'timer') {
            setButtonBackgroundColor("#DED700");
        }
    }, [type]);

    const announcementBarColorObject = useAnnouncementBarColor(
        bgSection,
        barBackgroundColor,
        gradient,
        backgroundPattern,
        backgroundGradientPosition
    );

 

    useEffect(() => {
        const styleSheet = document.styleSheets[0];
        const keyframesLeftToRight = `
            @keyframes marquee-left-to-right {
                0% { transform: translateX(-100%); }
                100% { transform: translateX(100%); }
            }
        `;

        const keyframesRightToLeft = `
            @keyframes marquee-right-to-left {
                100% { transform: translateX(-100%); }
                0% { transform: translateX(100%); }
            }
        `;

        const rules = [...styleSheet.cssRules];
        if (!rules.some((rule) => rule.name === "marquee-left-to-right")) {
            styleSheet.insertRule(keyframesLeftToRight, styleSheet.cssRules.length);
        }
        if (!rules.some((rule) => rule.name === "marquee-right-to-left")) {
            styleSheet.insertRule(keyframesRightToLeft, styleSheet.cssRules.length);
        }
    }, []);
    const animationName = marqueeDirection === "left" ? "marquee-right-to-left" : "marquee-left-to-right";



    return (
        <div
            className={`
                ${barPositionFixed == 'fixed' ? 'absolute left-0' : 'fixed'} 
                ${barPosition == 'bottom' ? 'bottom-0 left-0' : 'left-0 top-0'}  
                z-50  from-pink-400 to-yellow-400 overflow-hidden text-white shadow-lg flex items-center  !px-4
                    ${device === "phone"
                    ? "flex-col w-80 left-1/2 transform -translate-x-1/2 text-sm p-3 gap-2"
                    : "w-full text-lg p-2 gap-4"
                }
                    ${crossButton ? "block" : "hidden"}
                `}
            style={{
                transition: "width 0.3s ease, font-size 0.3s ease",
                fontFamily: designFontName ? designFontName : "initial",
                padding: heightBarRange
                    ? `${heightBarRange}px 0px`
                    : "auto",

                ...announcementBarColorObject,
            }}
        >
            <div className="max-w-full w-full overflow-hidden relative">
                <div
                    className="whitespace-nowrap"
                    style={{
                        animation: `${animationName} ${marqueeSpeed}s linear infinite`,
                        fontSize: `${designFontSize}px`,
                        color: `${designFontColor}`,
                        fontWeight: `${designFontType || '500'}`,
                    }}
                >
                    {announcementContent || "Exclusive Online Deal: Up to 50% Off!"}
                </div>
            </div>
        </div>
    );
};

export default MarqueeAnnouncementBar;
