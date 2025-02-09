interface FormProps {
  children: React.ReactNode,
  handleAction: (e: React.FormEvent<HTMLFormElement>) => Promise<void>;}

function Form({children, handleAction}: FormProps) {
    return (
        <div className="">
          <form onSubmit={handleAction}>
            {children}
          </form>
        </div>  
    )
}

export default Form;