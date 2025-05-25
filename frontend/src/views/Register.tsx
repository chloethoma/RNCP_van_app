import { useState } from "react";
import { Link, useNavigate } from "react-router";
import { registerUser } from "../services/api/apiRequests";
import FormSubmitButton from "../components/buttons/FormSubmitButton";
import Logo from "../assets/logo_transparent.svg";
import ErrorMessage from "../components/messages/ErrorMessage";
import { messages } from "../services/helpers/messagesHelper";
import FormInput from "../components/form/FormInput";

function Register() {
  const navigate = useNavigate();

  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [confirmedPassword, setConfirmedPassword] = useState("");
  const [pseudo, setPseudo] = useState("");
  const [passwordError, setPasswordError] = useState("");
  const [errorMessage, setErrorMessage] = useState<string | null>(null);

  async function handleRegistration(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();

    if (password !== confirmedPassword) {
      setPasswordError(messages.error_password_not_identical);
      return;
    } else {
      setPasswordError("");
    }

    try {
      const requestBody = { email, password, pseudo };
      await registerUser(requestBody);
      navigate("/");
    } catch (error) {
      setErrorMessage(
        error instanceof Error ? error.message : messages.error_register
      );
    }
  }

  return (
    <div className="relative flex items-center justify-center h-screen bg-light-green overflow-hidden">
      <ErrorMessage
        errorMessage={errorMessage}
        setErrorMessage={setErrorMessage}
      />

      {/* Header */}
      <div className="w-full max-w-sm p-8 pt-0 rounded-2xl border-2 border-border-grey shadow-lg">
        <div className="flex flex-col items-center">
          <img src={Logo} alt="Logo" className="w-8/12" />
        </div>
        <div className="flex flex-col items-center mb-2">
          <h2 className="text-2xl font-default font-semibold text-dark-grey text-center">
            Inscription
          </h2>
          <p className="text-xs text-dark-grey mt-1 px-4">
            Vous avez déjà un compte ?{" "}
            <Link
              to={"/login"}
              className="text-xs text-center text-light/90 font-medium hover:underline hover:text-light transition-colors duration-200"
            >
              Me connecter
            </Link>
          </p>
        </div>

        {/* Form */}
        <form onSubmit={handleRegistration}>
          <FormInput
            label={"Email"}
            type={"email"}
            placeholder={"Entrez votre email"}
            value={email}
            onChange={setEmail}
          />
          <FormInput
            label={"Pseudo"}
            type={"text"}
            placeholder={"Entrez votre pseudo"}
            value={pseudo}
            onChange={setPseudo}
          />
          <FormInput
            label={"Mot de passe"}
            type={"password"}
            placeholder={"Entrez votre mot de passe"}
            value={password}
            onChange={setPassword}
          />
          <FormInput
            label={"Confirmez votre mot de passe"}
            type={"password"}
            placeholder={"Confirmez votre mot de passe"}
            value={confirmedPassword}
            onChange={setConfirmedPassword}
          />
          {passwordError && (
            <p className="text-red text-xs mt-1">{passwordError}</p>
          )}
          <FormSubmitButton>S'inscrire</FormSubmitButton>
        </form>
      </div>
    </div>
  );
}

export default Register;
