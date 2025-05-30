import { useState } from "react";
import { Link, useNavigate } from "react-router";
import { registerUser } from "../services/api/apiRequests";
import FormSubmitButton from "../components/buttons/FormSubmitButton";
import Logo from "../assets/logo_transparent.svg";
import ErrorMessage from "../components/messages/ErrorMessage";
import { messages } from "../services/helpers/messagesHelper";
import FormInput from "../components/form/FormInput";
import getEntropy from "fast-password-entropy";

function Register() {
  const navigate = useNavigate();

  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [confirmedPassword, setConfirmedPassword] = useState("");
  const [pseudo, setPseudo] = useState("");
  const [passwordError, setPasswordError] = useState("");
  const [errorMessage, setErrorMessage] = useState<string | null>(null);

  // Calculate entropy of password. Minimal entropy for this app = 80
  const entropy = getEntropy(password);
  const isPasswordStrongEnough = entropy >= 80;

  async function handleRegistration(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();

    // Reset previous errors
    setPasswordError("");
    setErrorMessage("");

    // Validate password strength
    if (!isPasswordStrongEnough) {
      return setPasswordError(messages.error_password_not_strong);
    }

    // Validate password confirmation
    if (password !== confirmedPassword) {
      return setPasswordError(messages.error_password_not_identical);
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

  function handlePasswordChange(value: string) {
    setPassword(value);
    if (passwordError) {
      setPasswordError("");
    }
  }

  function handleConfirmedPasswordChange(value: string) {
    setConfirmedPassword(value);
    if (passwordError) {
      setPasswordError("");
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
            required
          />
          <FormInput
            label={"Pseudo"}
            type={"text"}
            placeholder={"Entrez votre pseudo"}
            value={pseudo}
            onChange={setPseudo}
            required
          />
          <FormInput
            label={"Mot de passe"}
            type={"password"}
            placeholder={"Entrez votre mot de passe"}
            value={password}
            onChange={handlePasswordChange}
            required
          />
          <FormInput
            label={"Confirmez votre mot de passe"}
            type={"password"}
            placeholder={"Confirmez votre mot de passe"}
            value={confirmedPassword}
            onChange={handleConfirmedPasswordChange}
            required
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
