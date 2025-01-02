import React, { useEffect, useState } from "react";
import useAnnouncementBarColor from "@/Hooks/useAnnouncementBarColor";
import useSimpleBarStore from "@/DataStores/SimpleBarStore";
import CountDownCounter from "../partial/simpleBar/CountDownCounter";
import useCountdown from "../../Hooks/useCountDown";


const SimpleAnnouncementBar = () => {
    const {
        type,
        device,
        crossButton,
        buttonPosition,
        isButtonShow,
        buttonStyle,
        buttonFontSelect,
        buttonFontWeight,
        buttonOutlineColor,
        designFontType,
        buttonText,
        buttonHeight,
        buttonFontSize,
        buttonWidth,
        buttonMargin,
        buttonTextColor,
        buttonBorderRadius,
        designFontSize,
        announcementContent,
        barCloseIconBgColor,
        designFontName,
        designFontColor,
        heightBarRange,
        barCloseIcon,
        barCloseIconColor,
        bgSection,
        barBackgroundColor,
        barPosition,
        barPositionFixed,
        backgroundPattern,
        backgroundGradientPosition,
        openInNewTab,
        buttonLink,
        buttonBackgroundColor,
        setButtonBackgroundColor,
        setCrossButton,
        bgGradientA,
        bgGradientB,
        startDateTime,
        endDateTime,
        selectedCountDownType
    } = useSimpleBarStore((state) => state);

    const { days, hours, minutes, seconds } = useCountdown(startDateTime, endDateTime, selectedCountDownType.hours);




    const handleNewTabButtonUrl = () => {
        openInNewTab
            ? window.open(buttonLink, "_blank", "noopener,noreferrer")
            : window.open(buttonLink, "_parent");
    };

    const getButtonStyle = (styleType, textColor, backgroundColor) => {
        switch (styleType) {
            case "outlined":
                return {
                    border: `1px solid ${textColor || "#051D52"}`,
                    backgroundColor: backgroundColor || "#ffffff",
                };
            case "dash":
                return {
                    border: `1px dashed ${textColor || "#051D52"}`,
                    backgroundColor: backgroundColor || "#ffffff",
                };
            default:
                return {
                    border: '1px solid transparent',
                    backgroundColor: backgroundColor || "#ffffff",
                };
        }
    };

    const handleCrossButton = () => {
        setCrossButton(false);
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

    return (
        <div className=" max-w-[1920px] mx-auto px-5 relative">
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
                <div className={`flex items-center ${device === "phone" ? "flex-col order-1" : ""}`}>
                    {buttonPosition === "Bar_Left" &&
                        isButtonShow &&
                        (buttonStyle === "link" ? (
                            <button
                                className={`text-sm transition duration-300 min-w-max ${device === "phone"
                                    ? "px-2 py-1"
                                    : "px-2 py-1 mr-4"
                                    }`}
                                style={{
                                    position: `${device === "phone"
                                        ? "static"
                                        : "absolute"
                                        }`,
                                    top: '9px',
                                    left: `calc(0% + ${buttonMargin}px)`,
                                    fontFamily: buttonFontSelect || "initial",
                                    lineHeight: "1.25rem",
                                    fontWeight: buttonFontWeight || "600",
                                    color: buttonTextColor || "#051D52",
                                    textDecoration: "underline",
                                    textDecorationColor:
                                        buttonTextColor || "#051D52",
                                    textDecorationThickness: "1px",
                                    textUnderlineOffset: "4px",
                                }}
                                onClick={handleNewTabButtonUrl}
                            >
                                {buttonText || "Shop Now"}
                            </button>
                        ) : (
                            <button
                                className={`text-sm transition duration-300 min-w-max ${device === "phone"
                                    ? "px-2 py-1"
                                    : "px-2 py-1 ml-4"
                                    }`}
                                onClick={handleNewTabButtonUrl}
                                style={{
                                    position: `${device === "phone"
                                        ? "static"
                                        : "absolute"
                                        }`,
                                    top: '9px',
                                    left: `calc(0% + ${buttonMargin}px)`,
                                    fontFamily: buttonFontSelect || "initial",
                                    lineHeight: "1.25rem",
                                    fontWeight: buttonFontWeight || "600",
                                    color: buttonTextColor || "#051D52",
                                    ...getButtonStyle(
                                        buttonStyle,
                                        buttonTextColor,
                                        buttonBackgroundColor
                                    ),
                                    borderRadius: buttonBorderRadius
                                        ? `${buttonBorderRadius}px`
                                        : "4px",
                                    paddingTop: buttonHeight
                                        ? buttonHeight
                                        : "0px",
                                    paddingBottom: buttonHeight
                                        ? buttonHeight
                                        : "0px",
                                    paddingRight: buttonWidth
                                        ? buttonWidth
                                        : "0px",
                                    paddingLeft: buttonWidth
                                        ? buttonWidth
                                        : "0px",
                                    fontSize: buttonFontSize
                                        ? buttonFontSize
                                        : "14px",
                                }}
                            >
                                {buttonText || "Shop Now"}
                            </button>
                        ))}
                </div>

                <div className={`flex flex-wrap flex-1  mx-auto items-center justify-center  gap-2 ${device === "phone" ? "flex-col" : "flex-row"
                    }`}
                >


                    <div
                        className={` max-w-[90%]  mx-auto flex flex-wrap  items-center justify-center  gap-3  font-semibold break-normal leading-normal relative   ${device === "phone" ? "flex-col " : "flex-row m-0"
                            }`}
                        style={{
                            fontSize: `${designFontSize}px`,
                            color: `${designFontColor}`,
                            fontWeight: `${designFontType || '500'}`,

                        }}
                    >
                        {
                            buttonPosition === "Text_Left" &&
                            isButtonShow &&
                            (buttonStyle === "link" ? (
                                <button
                                    className={`text-sm transition duration-300 min-w-max ${device === "phone"
                                        ? "px-2 py-1 order-1"
                                        : "px-2 py-1"
                                        }`}
                                    style={{
                                        position: `${device === "phone"
                                            ? "static"
                                            : "absolute"
                                            }`,
                                        top: '-4px',
                                        right: `calc(100% + ${buttonMargin + 16}px)`,
                                        fontFamily: buttonFontSelect || "initial",
                                        lineHeight: "1.25rem",
                                        fontWeight: buttonFontWeight || "600",
                                        color: buttonTextColor || "#051D52",
                                        textDecoration: "underline",
                                        textDecorationColor:
                                            buttonTextColor || "#051D52",
                                        textDecorationThickness: "1px",
                                        textUnderlineOffset: "4px",
                                    }}
                                    onClick={handleNewTabButtonUrl}
                                >
                                    {buttonText || "Shop Now"}
                                </button>
                            ) : (
                                <button
                                    className={`text-sm transition duration-300 min-w-max ${device === "phone"
                                        ? "px-2 py-1 order-1"
                                        : "px-2 py-1"
                                        }`}
                                    onClick={handleNewTabButtonUrl}
                                    style={{
                                        position: `${device === "phone"
                                            ? "static"
                                            : "absolute"
                                            }`,
                                        top: '-4px',
                                        right: `calc(100% + ${buttonMargin + 16}px)`,
                                        fontFamily: buttonFontSelect || "initial",
                                        lineHeight: "1.25rem",
                                        fontWeight: buttonFontWeight || "600",
                                        color: buttonTextColor || "#051D52",
                                        ...getButtonStyle(
                                            buttonStyle,
                                            buttonTextColor,
                                            buttonBackgroundColor
                                        ),
                                        borderRadius: buttonBorderRadius
                                            ? `${buttonBorderRadius}px`
                                            : "4px",
                                        paddingTop: buttonHeight
                                            ? buttonHeight
                                            : "0px",
                                        paddingBottom: buttonHeight
                                            ? buttonHeight
                                            : "0px",
                                        paddingRight: buttonWidth
                                            ? buttonWidth
                                            : "0px",
                                        paddingLeft: buttonWidth
                                            ? buttonWidth
                                            : "0px",
                                        fontSize: buttonFontSize
                                            ? buttonFontSize
                                            : "14px",
                                    }}
                                >
                                    {buttonText || "Shop Now"}
                                </button>
                            ))}

                        <span>
                            {
                                announcementContent
                                    ? announcementContent
                                    : "Exclusive Online Deal: Up to 50% Off!"
                            }
                        </span>

                        {
                            type === 'timer' &&
                            <CountDownCounter
                                days={days}
                                hours={hours}
                                minutes={minutes}
                                seconds={seconds}
                            />
                        }


                        {
                        buttonPosition === "Text_Right" &&
                            isButtonShow &&
                            (buttonStyle === "link" ? (
                                <button
                                    className={`text-sm transition duration-300 min-w-max ${device === "phone"
                                        ? "px-2 py-1"
                                        : "px-2 py-1 mr-3"
                                        }`}
                                    style={{
                                        position: `${device === "phone"
                                            ? "static"
                                            : "absolute"
                                            }`,
                                        top: '0',
                                        left: `calc(100% + ${buttonMargin + 16}px)`,
                                        fontFamily: buttonFontSelect || "initial",
                                        lineHeight: "1.25rem",
                                        fontWeight: buttonFontWeight || "600",
                                        color: buttonTextColor || "#051D52",
                                        textDecoration: "underline",
                                        textDecorationColor:
                                            buttonTextColor || "#051D52",
                                        textDecorationThickness: "1px",
                                        textUnderlineOffset: "4px",
                                    }}
                                    onClick={handleNewTabButtonUrl}
                                >
                                    {buttonText || "Shop Now"}
                                </button>
                            ) : (
                                <button
                                    className={`text-sm transition duration-300 min-w-max ${device === "phone"
                                        ? "px-2 py-1"
                                        : "px-2 py-1 "
                                        }`}
                                    onClick={handleNewTabButtonUrl}
                                    style={{
                                        position: `${device === "phone"
                                            ? "static"
                                            : "absolute"
                                            }`,
                                        top: 'auto',
                                        left: `calc(100% + ${buttonMargin + 16}px)`,
                                        fontFamily: buttonFontSelect || "initial",
                                        lineHeight: "1.25rem",
                                        fontWeight: buttonFontWeight || "600",
                                        color: buttonTextColor || "#051D52",
                                        ...getButtonStyle(
                                            buttonStyle,
                                            buttonOutlineColor,
                                            buttonBackgroundColor
                                        ),
                                        borderRadius: buttonBorderRadius
                                            ? `${buttonBorderRadius}px`
                                            : "4px",
                                        paddingTop: buttonHeight
                                            ? buttonHeight
                                            : "0px",
                                        paddingBottom: buttonHeight
                                            ? buttonHeight
                                            : "0px",
                                        paddingRight: buttonWidth
                                            ? buttonWidth
                                            : "0px",
                                        paddingLeft: buttonWidth
                                            ? buttonWidth
                                            : "0px",
                                        fontSize: buttonFontSize
                                            ? buttonFontSize
                                            : "14px",
                                    }}
                                >
                                    {buttonText || "Shop Now"}
                                </button>
                            ))}
                    </div>


                </div>

                <div className={`flex items-center h-full  ${device === "phone" ? "flex-col order-1 pe-0" : "pe-12"}`}>
                    {buttonPosition === "Bar_Right" &&
                        isButtonShow &&
                        (buttonStyle === "link" ? (
                            <button
                                className={`text-sm transition duration-300 min-w-max ${device === "phone"
                                    ? "px-2 py-1"
                                    : "px-2 py-1 "
                                    }`}
                                style={{
                                    position: `${device === "phone"
                                        ? "static"
                                        : "absolute"
                                        }`,
                                    top: '9px',
                                    right: `calc(4% + ${buttonMargin}px)`,
                                    fontFamily: buttonFontSelect || "initial",
                                    lineHeight: "1.25rem",
                                    fontWeight: buttonFontWeight || "600",
                                    color: buttonTextColor || "#051D52",
                                    textDecoration: "underline",
                                    textDecorationColor:
                                        buttonTextColor || "#051D52",
                                    textDecorationThickness: "1px",
                                    textUnderlineOffset: "4px",
                                }}
                                onClick={handleNewTabButtonUrl}
                            >
                                {buttonText || "Shop Now"}
                            </button>
                        ) : (
                            <button
                                className={`text-sm transition duration-300 min-w-max ${device === "phone"
                                    ? "px-2 py-1"
                                    : "px-2 py-1 ml-4"
                                    }`}
                                onClick={handleNewTabButtonUrl}
                                style={{
                                    position: `${device === "phone"
                                        ? "static"
                                        : "absolute"
                                        }`,
                                    top: '9px',
                                    right: `calc(4% + ${buttonMargin}px)`,
                                    fontFamily: buttonFontSelect || "initial",
                                    lineHeight: "1.25rem",
                                    fontWeight: buttonFontWeight || "600",
                                    color: buttonTextColor || "#051D52",
                                    ...getButtonStyle(
                                        buttonStyle,
                                        buttonTextColor,
                                        buttonBackgroundColor
                                    ),
                                    borderRadius: buttonBorderRadius
                                        ? `${buttonBorderRadius}px`
                                        : "4px",
                                    paddingTop: buttonHeight
                                        ? buttonHeight
                                        : "0px",
                                    paddingBottom: buttonHeight
                                        ? buttonHeight
                                        : "0px",
                                    paddingRight: buttonWidth
                                        ? buttonWidth
                                        : "0px",
                                    paddingLeft: buttonWidth
                                        ? buttonWidth
                                        : "0px",
                                    fontSize: buttonFontSize
                                        ? buttonFontSize
                                        : "14px",
                                }}
                            >
                                {buttonText || "Shop Now"}
                            </button>
                        ))}
                </div>
                {/* Medium-sized Close Button positioned slightly higher */}
                {barCloseIcon != "hidden" && (
                    <button
                        className={`absolute top-1/2 transform -translate-y-1/2 rounded-full text-lg font-bold flex items-center justify-center hover:bg-white/50 transition duration-300 ${device === "phone"
                            ? "right-2 w-5 h-5"
                            : "right-4 w-6 h-6"
                            }`}
                        onClick={handleCrossButton}
                        style={{
                            color: barCloseIconColor
                                ? barCloseIconColor
                                : "#fff",
                            backgroundColor:
                                barCloseIcon == "onlyIcon"
                                    ? "transparent"
                                    : barCloseIconBgColor,
                        }}
                    >
                        &times;
                    </button>
                )}
            </div>
        </div>
    );
};

export default SimpleAnnouncementBar;
