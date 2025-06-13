import { messages } from "./messagesHelper";

type Error = {
  code: string;
  message: string;
  target?: string;
  details?: ErrorDetails;
};

type ErrorDetails = {
  code: string;
  target: string;
  message: string;
};

type ErrorTranslations = {
  [key: string]: { [message: string]: string };
};

const errorTranslations: ErrorTranslations = {
  BadRequest: {
    "Some params sent are not correct or not formatted correctly":
      "Certains paramètres sont incorrects ou mal formatés.",
    "Errors found in submitted data": "Erreur",
  },
  Unauthorized: {
    "You are not authorized to perform this action":
      "Vous n'êtes pas autorisé à effectuer cette action.",
    "No authenticated user found in JWT":
      "Votre session a expiré. Veuillez vous reconnecter.",
    "Bad credentials":
      "Les identifiants ne sont pas corrects, veuillez réessayer.",
  },
  AccessDenied: {
    "Invalid JWT token": "Erreur d'authentification",
    "Missing JWT token": "Erreur d'authentification",
    "Token expired, please renew it.":
      "Votre session a expirée, veuillez vous reconnecter.",
  },
  NotFound: {
    "Friendship not found": "Cette amitié n'existe pas.",
    "Spot not found": "Le spot demandé est introuvable.",
    "User Not Found": "Aucun utilisateur correspondant n’a été trouvé.",
  },
  Conflict: {
    "Friendship already exists": "Cette amitié existe déjà !",
    "User already exists": "Erreur",
  },
  BadRequestDetails: {
    "The receiver id cannot be the same as the requester id":
      "Vous ne pouvez pas vous envoyer une demande à vous-même.",
    "Current password is incorrect": "Le mot de passe actuel est incorrect.",
    "This value is too short. It should have 8 characters or more.":
      "Le mot de passe doit faire 8 caractères minimum.",
    "User already exists with this email": "Cet email est déjà utilisé",
    "User already exists with this pseudo": "Ce pseudo est déjà utilisé",
  },
};

const translateMessage = (message: string): string => {
  for (const translations of Object.values(errorTranslations)) {
    if (translations[message]) {
      return translations[message];
    }
  }
  return message;
};

export const translateErrorMessage = (error: Error): string => {
  const mainMessage = translateMessage(error.message) || messages.error_default;

  let formattedDetails = "";

  if (error.details && Array.isArray(error.details)) {
    formattedDetails = error.details
      .map(({ message }) => {
        const translatedMessage = translateMessage(message);
        return `• ${translatedMessage}`;
      })
      .join("\n");
  }

  return [mainMessage, formattedDetails].filter(Boolean).join("\n\n");
};
