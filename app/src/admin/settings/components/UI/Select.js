import Select from 'react-select'

export default (props) => {
    const theme = (theme) => ({
        ...theme,
        borderRadius: ".5rem",
        colors: {
          ...theme.colors,
          primary25: "rgb(236 248 244)",
          primary50: "rgb(204, 204, 204)",
          primary: "rgb(7 152 18)"
        }
      })

    return (
        <>
            <Select
                theme={theme}
                {...props}
            />
        </>
    );
}