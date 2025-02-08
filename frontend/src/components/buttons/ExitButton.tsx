import Button from "./Button";
import { X } from 'lucide-react';

interface ExitButtonProps {
    onClick: (event: React.MouseEvent<HTMLButtonElement>) => void;
}

function ExitButton ({onClick}: ExitButtonProps)
{
    return (
        <Button onClick={onClick} size={"small"}>
            <X size={20}/>
        </Button>
    )
}

export default ExitButton;
