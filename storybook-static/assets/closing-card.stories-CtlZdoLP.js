import{c as j,a as _,j as o}from"./utils-q4EKThuO.js";import{B as N}from"./badge-DghL8o_I.js";import{C as b,a as y}from"./card-tJerkyCy.js";import{C as E}from"./currency-CQP0FS4i.js";import{m as x,a as O}from"./index-CSrV_1Wb.js";import"./iframe-DOYQQv_f.js";import"./preload-helper-PPVm8Dsz.js";import"./index-BZoqsebz.js";const k=h=>{const s=j.c(20),{closing:e,className:u,onClick:i}=h,C=i&&"cursor-pointer hover:shadow-md transition-shadow";let t;s[0]!==u||s[1]!==C?(t=_("cursor-default",C,u),s[0]=u,s[1]=C,s[2]=t):t=s[2];let r;s[3]!==e.ct_number?(r=o.jsx("p",{className:"text-xs font-mono text-muted-foreground",children:e.ct_number}),s[3]=e.ct_number,s[4]=r):r=s[4];let a;s[5]!==e.opening_amount?(a=o.jsxs("p",{className:"font-semibold text-sm mt-0.5",children:["Opening: ",o.jsx(E,{value:e.opening_amount})]}),s[5]=e.opening_amount,s[6]=a):a=s[6];let n;s[7]!==r||s[8]!==a?(n=o.jsxs("div",{className:"min-w-0",children:[r,a]}),s[7]=r,s[8]=a,s[9]=n):n=s[9];const f=e.status==="OPEN"?"default":"secondary";let c;s[10]!==e.status||s[11]!==f?(c=o.jsx(N,{variant:f,className:"text-xs shrink-0",children:e.status}),s[10]=e.status,s[11]=f,s[12]=c):c=s[12];let l;s[13]!==n||s[14]!==c?(l=o.jsxs(b,{className:"p-4 flex items-center justify-between gap-3",children:[n,c]}),s[13]=n,s[14]=c,s[15]=l):l=s[15];let m;return s[16]!==i||s[17]!==t||s[18]!==l?(m=o.jsx(y,{className:t,onClick:i,children:l}),s[16]=i,s[17]=t,s[18]=l,s[19]=m):m=s[19],m};k.__docgenInfo={description:"",methods:[],displayName:"ClosingCard"};const q={title:"Elements/Closing/ClosingCard",component:k,tags:["autodocs"],parameters:{layout:"centered"}},d={args:{closing:x}},p={args:{closing:O}},g={args:{closing:x,onClick:()=>alert("Closing clicked")}};d.parameters={...d.parameters,docs:{...d.parameters?.docs,source:{originalSource:`{
  args: {
    closing: mockClosing
  }
}`,...d.parameters?.docs?.source}}};p.parameters={...p.parameters,docs:{...p.parameters?.docs,source:{originalSource:`{
  args: {
    closing: mockClosingClosed
  }
}`,...p.parameters?.docs?.source}}};g.parameters={...g.parameters,docs:{...g.parameters?.docs,source:{originalSource:`{
  args: {
    closing: mockClosing,
    onClick: () => alert('Closing clicked')
  }
}`,...g.parameters?.docs?.source}}};const z=["Open","Closed","Clickable"];export{g as Clickable,p as Closed,d as Open,z as __namedExportsOrder,q as default};
