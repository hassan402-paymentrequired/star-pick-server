import React from "react";
import AuthLayout from "../layouts/auth-layout";
import { RegisterForm } from "@/components/ui/auth/register-form";

const Register = () => {
    return (
        <AuthLayout>
            <div className="flex w-full h-full items-center">
                <RegisterForm />
            </div>
        </AuthLayout>
    );
};

export default Register;
