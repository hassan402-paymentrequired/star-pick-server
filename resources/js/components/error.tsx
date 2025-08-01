import { cn } from "@/lib/utils";
import React from "react";

const FormError = ({ message, classNames }: {message: string, classNames?: string}) => {
    return (
        <div className={cn("text-red-400 text-xs", classNames)}>{message}</div>
    );
};

export default FormError;
