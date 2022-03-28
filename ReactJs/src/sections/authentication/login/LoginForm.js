import * as Yup from 'yup';
import {useEffect, useState} from 'react';
import {Link as RouterLink, Navigate, useNavigate} from 'react-router-dom';
import { useFormik, Form, FormikProvider } from 'formik';
// material
import {
  Link,
  Stack,
  Checkbox,
  TextField,
  IconButton,
  InputAdornment,
  FormControlLabel
} from '@mui/material';
import { LoadingButton } from '@mui/lab';
// component
import Iconify from '../../../components/Iconify';
import axios from "axios";
import {useCookies} from "react-cookie";

// ----------------------------------------------------------------------

export default function LoginForm() {
  const navigate = useNavigate();
  const [showPassword, setShowPassword] = useState(false);
  const [sRedirectTo, setRedirectTo] = useState("");
  const [cookies, setCookie, removeCookie] = useCookies(['cookie-name']);

    useEffect(() => {
        console.log()
        authFromCookie();
        // code to run on component mount
    }, [])

    const authFromCookie = () => {
        const sToken = cookies.accessToken ?? null
        if (!sToken)
            return;

        const oInstance = axios.create({
            baseURL: process.env.REACT_APP_API_URL,
            headers: {'Authorization': 'Bearer ' + sToken}
        })

        oInstance.post("/api/auth/me")
            .then((oResponse) => {
                setRedirectTo("/dashboard/user")
            }).catch(() => {
                removeCookie("accessToken")
            })
    }

    const LoginSchema = Yup.object().shape({
    email: Yup.string().email('Email must be a valid email address').required('Email is required'),
    password: Yup.string().required('Password is required')
  });

  const formik = useFormik({
    initialValues: {
      email: '',
      password: '',
    },
    validationSchema: LoginSchema,
    onSubmit: () => {
      const oInstance = axios.create({
        baseURL: process.env.REACT_APP_API_URL,
      })

      oInstance.post("/api/auth/login", formik.values)
          .then((oResponse) => {
              console.log(oResponse.data)
              if (oResponse.status != 200) {
                  formik.setErrors({password: "Authentification failed"})
                  formik.setSubmitting(false);
              }
              formik.setSubmitting(false);
              setCookie("accessToken", oResponse.data.access_token.token)
              setRedirectTo("/dashboard/user")
          }).catch(() => {
          formik.setErrors({password: "Authentification failed"})
          formik.setSubmitting(false);
      })
    }
  });

  const { errors, touched, setErrors, values, isSubmitting, handleSubmit, getFieldProps } = formik;

  const handleShowPassword = () => {
    setShowPassword((show) => !show);
  };

    if (sRedirectTo != "")
    return <Navigate to={sRedirectTo} />

    return (
    <FormikProvider value={formik}>
      <Form autoComplete="off" noValidate onSubmit={handleSubmit}>
        <Stack spacing={3}>
          <TextField
            fullWidth
            autoComplete="username"
            type="email"
            label="Email address"
            {...getFieldProps('email')}
            error={Boolean(touched.email && errors.email)}
            helperText={touched.email && errors.email}
          />

          <TextField
            fullWidth
            autoComplete="current-password"
            type={showPassword ? 'text' : 'password'}
            label="Password"
            {...getFieldProps('password')}
            InputProps={{
              endAdornment: (
                <InputAdornment position="end">
                  <IconButton onClick={handleShowPassword} edge="end">
                    <Iconify icon={showPassword ? 'eva:eye-fill' : 'eva:eye-off-fill'} />
                  </IconButton>
                </InputAdornment>
              )
            }}
            error={Boolean(touched.password && errors.password)}
            helperText={touched.password && errors.password}
          />
        </Stack>

        <LoadingButton
          fullWidth
          size="large"
          type="submit"
          variant="contained"
          loading={isSubmitting}
        >
          Login
        </LoadingButton>
      </Form>
    </FormikProvider>
  );
}
